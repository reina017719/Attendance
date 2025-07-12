@extends('layouts.user')

@section('css')
<link rel="stylesheet" href="{{ asset('css/list.css') }}">
@endsection

@section('content')
<div class="content">
    <div class="content__inner">
        @php
            use Carbon\Carbon;
            use Carbon\CarbonPeriod;

            $currentYear = request()->input('year', now()->year);
            $currentMonth = request()->input('month', now()->month);

            $currentDate = \Carbon\Carbon::createFromDate($currentYear, $currentMonth, 1);
            $prevDate = $currentDate->copy()->subMonth();
            $nextDate = $currentDate->copy()->addMonth();

            $startOfMonth = $currentDate->copy()->startOfMonth();
            $endOfMonth = $currentDate->copy()->endOfMonth();
            $period = CarbonPeriod::create($startOfMonth, $endOfMonth);

            $weekdayJP = ['日', '月', '火', '水', '木', '金', '土'];

            $attendanceMap = $attendances->keyBy(function ($a) {
                return \Carbon\Carbon::parse($a->work_date)->toDateString();
            });
        @endphp
        <h2>勤怠一覧</h2>
        <div class="controls">
            <a class="nav-btn" href="?year={{ $prevDate->year }}&month={{ $prevDate->month }}">← 前月</a>
            <div class="current-month">
                <span class="calender-icon">📅</span> {{ $currentDate->format('Y/m') }}
            </div>
            <a class="nav-btn" href="?year={{ $nextDate->year }}&month={{ $nextDate->month }}">翌月 →</a>
        </div>
        <table>
            <thead>
                <tr>
                    <th>日付</th>
                    <th>出勤</th>
                    <th>退勤</th>
                    <th>休憩</th>
                    <th>合計</th>
                    <th>詳細</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($period as $date)
                    @php
                        $attendance = $attendanceMap[$date->toDateString()] ?? null;
                        $correction = $attendance ? optional($attendance->approvedCorrectionRequest) : null;

                        $startTime = optional($correction)->requested_start_time ?? optional($attendance)->start_time;
                        $endTime = optional($correction)->requested_end_time ?? optional($attendance)->end_time;

                        $totalBreakMinutes = 0;

                        if ($correction && $correction->requested_break1_start_time && $correction->requested_break1_end_time) {
                            $totalBreakMinutes += \Carbon\Carbon::parse($correction->requested_break1_start_time)
                            ->diffInMinutes(\Carbon\Carbon::parse($correction->requested_break1_end_time));
                        }
                        if ($correction && $correction->requested_break2_start_time && $correction->requested_break2_end_time) {
                            $totalBreakMinutes += \Carbon\Carbon::parse($correction->requested_break2_start_time)
                            ->diffInMinutes(\Carbon\Carbon::parse($correction->requested_break2_end_time));
                        }

                        if ($totalBreakMinutes === 0 && $attendance && $attendance->breakTimes && $attendance->breakTimes->isNotEmpty()) {
                            foreach ($attendance->breakTimes as $break) {
                                if ($break->break_start && $break->break_end) {
                                    $start = \Carbon\Carbon::parse($break->break_start);
                                    $end = \Carbon\Carbon::parse($break->break_end);
                                    $totalBreakMinutes += $start->diffInMinutes($end);
                                }
                            }
                        }

                        $breakHour = floor($totalBreakMinutes / 60);
                        $breakMin = str_pad($totalBreakMinutes % 60, 2, '0', STR_PAD_LEFT);

                        $workHour = $workMin = null;
                        if ($startTime && $endTime) {
                            $workMinutes = \Carbon\Carbon::parse($startTime)->diffInMinutes(\Carbon\Carbon::parse($endTime)) - $totalBreakMinutes;
                            $workHour = floor($workMinutes / 60);
                            $workMin = str_pad($workMinutes % 60, 2, '0', STR_PAD_LEFT);
                        }
                    @endphp
                <tr>
                    <td><strong>{{ $date->format('m/d') }} ({{ $weekdayJP[$date->dayOfWeek] }})</strong></td>
                    <td>{{ $startTime ? \Carbon\Carbon::parse($startTime)->format('H:i') : '' }}</td>
                    <td>{{ $endTime ? \Carbon\Carbon::parse($endTime)->format('H:i') : '' }}</td>
                    <td>{{ $totalBreakMinutes > 0 ? "$breakHour:$breakMin" : '' }}</td>
                    <td>{{ $workHour !== null ? "$workHour:$workMin" : '' }}</td>
                    <td>
                        @if ($attendance)
                        <a href="{{ route('attendance.show', ['id' => $attendance->id]) }}">詳細</a>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection