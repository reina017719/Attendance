<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\CorrectionRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use App\Models\User;
use App\Models\Attendance;
use App\Models\BreakTime;
use App\Models\AttendanceCorrectionRequest;


use Carbon\Carbon;

class AttendanceController extends Controller
{
    public function index()
    {
        return view('user.attendance.index');
    }

    public function clockIn()
    {
        $user = Auth::user();
        $today = Carbon::today();

        $attendance = Attendance::firstOrCreate(
            ['user_id' => $user->id, 'work_date' => $today],
            ['start_time' => now(), 'status' => 'working']
        );

        return response()->noContent();
    }

    public function breakStart()
    {
        $user = Auth::user();
        $attendance = Attendance::where('user_id', $user->id)->where('work_date', Carbon::today())->first();

        if ($attendance) {
            $attendance->status = 'on_break';
            $attendance->save();

            BreakTime::create([
                'attendance_id' => $attendance->id,
                'break_start' => now()->format('H:i:s'),
            ]);
        }

        return response()->noContent();
    }

    public function breakEnd()
    {
        $user = Auth::user();
        $attendance = Attendance::where('user_id', $user->id)->where('work_date', Carbon::today())->first();

        if ($attendance) {
            $attendance->status = 'working';
            $attendance->save();

            $latestBreak = BreakTime::where('attendance_id', $attendance->id)->whereNull('break_end')->latest('break_start')->first();

            if ($latestBreak) {
                $latestBreak->break_end = now()->format('H:i:s');
                $latestBreak->save();
            }
        }

        return response()->noContent();
    }

    public function clockOut()
    {
        $user = Auth::user();
        $attendance = Attendance::where('user_id', $user->id)->where('work_date', Carbon::today())->first();

        if ($attendance) {
            $attendance->end_time = now();
            $attendance->status = 'finished';

            if ($attendance->start_time && $attendance->end_time) {
                $workMinutes = Carbon::parse($attendance->end_time)->diffInMinutes(Carbon::parse($attendance->start_time));

                $breaks = BreakTime::where('attendance_id', $attendance->id)->get();
                $totalBreakMinutes = $breaks->reduce(function ($carry, $break) {
                    if ($break->break_end) {
                        return $carry + Carbon::parse($break->break_end)->diffInMinutes(Carbon::parse($break->break_start));
                    }
                    return $carry;
                }, 0);

                $attendance->total_work_minutes = $workMinutes - $totalBreakMinutes;
            }

            $attendance->save();
        }
        return response()->noContent();
    }

    public function attendanceList(Request $request)
    {
        $user = Auth::user();

        $year = $request->input('year', now()->year);
        $month = $request->input('month', now()->month);

        $attendances = Attendance::with(['breakTimes', 'approvedCorrectionRequest'])
        ->where('user_id', $user->id)
        ->whereYear('work_date', $year)->whereMonth('work_date', $month)
        ->orderBy('work_date', 'asc')
        ->get();

        return view('user.attendance.list', compact('attendances'));
    }

    public function attendanceShow($id)
    {
        $attendance = Attendance::with(['user', 'breakTimes', 'correctionRequests'])->findOrFail($id);

        $pendingRequest = $attendance->correctionRequests()
        ->whereIn('status', ['pending', 'approved'])
        ->latest()
        ->first();

        return view('user.attendance.show', [
        'attendance' => $attendance,
        'pendingRequest' => $pendingRequest,
        ]);
    }

    public function storeCorrectionRequest(CorrectionRequest $request)
    {
        AttendanceCorrectionRequest::create([
            'user_id' => Auth::id(),
            'attendance_id' => $request->attendance_id,
            'break_id' => null,
            'requested_start_time' => $request->requested_start_time,
            'requested_end_time' => $request->requested_end_time,
            'requested_break1_start_time' => $request->requested_break1_start_time,
            'requested_break1_end_time' => $request->requested_break1_end_time,
            'requested_break2_start_time' => $request->requested_break2_start_time,
            'requested_break2_end_time' => $request->requested_break2_end_time,
            'reason' => $request->reason,
            'status' => 'pending',
        ]);

        return redirect()->back();
    }

    public function attendanceRequest()
    {
        $requests = AttendanceCorrectionRequest::with(['user', 'attendance'])
        ->orderBy('created_at', 'desc')
        ->get();

        return view('user.stamp_correction_request.list', compact('requests'));
    }

    public function adminAttendanceList(Request $request)
    {
        $date = $request->input('date', now()->toDateString());

        $attendances = Attendance::with('user', 'breakTimes', 'approvedCorrectionRequest')
        ->where('work_date', $date)
        ->get();

        return view('admin.attendance.list', compact('attendances', 'date'));
    }

    public function staffList()
    {
        $staff = User::where('role', '!=', 'admin')->get();

        return view('admin.staff.list', compact('staff'));
    }

    public function staffShow($id)
    {
        $user = User::findOrFail($id);

        $year = request()->input('year', now()->year);
        $month = request()->input('month', now()->month);

        $dateObj = Carbon::createFromDate($year, $month, 1);
        $startOfMonth = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $endOfMonth = Carbon::createFromDate($year, $month, 1)->endOfMonth();

        $prevDate = $dateObj->copy()->subMonth();
        $nextDate = $dateObj->copy()->addMonth();

        $attendances = Attendance::with(['breakTimes', 'approvedCorrectionRequest'])
        ->where('user_id', $user->id)
        ->whereBetween('work_date', [$startOfMonth, $endOfMonth])
        ->get();

        $attendanceMap = $attendances->keyBy(function ($attendance) {
            return Carbon::parse($attendance->work_date)->toDateString();
        });

        return view('admin.attendance.staff_show', compact('attendances', 'user', 'prevDate', 'nextDate', 'dateObj', 'attendanceMap'));
    }

    public function exportCsv($id)
    {
        $user = User::findOrFail($id);
        $year = request()->input('year', now()->year);
        $month = request()->input('month', now()->month);

        $startOfMonth = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $endOfMonth = Carbon::createFromDate($year, $month, 1)->endOfMonth();

        $attendances = Attendance::with('breakTimes', 'approvedCorrectionRequest')
        ->where('user_id', $user->id)
        ->whereBetween('work_date', [$startOfMonth, $endOfMonth])
        ->get();

        $csvData = [
            ['日付', '出勤', '退勤', '休憩', '合計']
        ];

        foreach ($attendances as $attendance) {
            $correction = optional($attendance->approvedCorrectionRequest);

            $startTime = $correction->requested_start_time ?? $attendance->start_time;
            $endTime = $correction->requested_end_time ?? $attendance->end_time;

            $totalBreakMinutes = 0;

            if ($correction && $correction->requested_break1_start_time && $correction->requested_break1_end_time) {
                $totalBreakMinutes += Carbon::parse($correction->requested_break1_start_time)
                ->diffInMinutes(Carbon::parse($correction->requested_break1_end_time));
            }
            if ($correction && $correction->requested_break2_start_time && $correction->requested_break2_end_time) {
                $totalBreakMinutes += Carbon::parse($correction->requested_break2_start_time)
                ->diffInMinutes(Carbon::parse($correction->requested_break2_end_time));
            }
            if ($totalBreakMinutes === 0 && $attendance->breakTimes && $attendance->breakTimes->isNotEmpty()) {
                foreach ($attendance->breakTimes as $break) {
                    if ($break->break_start && $break->break_end) {
                        $totalBreakMinutes += Carbon::parse($break->break_start)
                        ->diffInMinutes(Carbon::parse($break->break_end));
                    }
                }
            }

            $breakFormatted = sprintf('%02d:%02d', floor($totalBreakMinutes / 60), $totalBreakMinutes % 60);

            $workFormatted = '';
            if ($startTime && $endTime) {
                $workMinutes = Carbon::parse($startTime)->diffInMinutes(Carbon::parse($endTime)) - $totalBreakMinutes;
                $workFormatted = sprintf('%02d:%02d', floor($workMinutes / 60), $workMinutes % 60);
            }

            $csvData[] = [
                Carbon::parse($attendance->work_date)->format('Y/m/d'),
                $startTime ? Carbon::parse($startTime)->format('H:i') : '',
                $endTime ? Carbon::parse($endTime)->format('H:i') : '',
                $breakFormatted,
                $workFormatted
            ];
        }

        $filename = $user->name . "_attendance_{$year}_{$month}.csv";
        $handle = fopen('php://temp', 'r+');
        foreach ($csvData as $line) {
            fputcsv($handle, $line);
        }
        rewind($handle);

        return Response::stream(function () use ($handle) {
            fpassthru($handle);
        }, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename={$filename}",
        ]);
    }

    public function approve(AttendanceCorrectionRequest $attendance_correction_request)
    {
        $attendance_correction_request->load(['user', 'attendance']);

        return view('admin.stamp_correction_request.approve', [
        'request' => $attendance_correction_request]);
    }

    public function approveUpdate(AttendanceCorrectionRequest $attendance_correction_request)
    {
        $attendance_correction_request->status = 'approved';
        $attendance_correction_request->save();

        return redirect()->route('stamp_correction_request.approve', ['attendance_correction_request' => $attendance_correction_request->id]);
    }
}