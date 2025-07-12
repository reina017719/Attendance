@extends('layouts.admin')

@section('css')
<link rel="stylesheet" href="{{ asset('css/list.css') }}">
@endsection

@section('content')
<div class="content">
    <div class="content__inner">
        <h2>{{ $user->name }}さんの勤怠</h2>
        <div class="controls">
            <a class="nav-btn" href="?year={{ $prevDate->year }}&month={{ $prevDate->month }}">← 前月</a>
            <div class="current-month"><span class="calender-icon">📅</span> {{ $dateObj->format('Y/m') }}</div>
            <a class="nav-btn" href="?year={{ $nextDate->year }}&month={{ $nextDate->month }}">翌月 →</a>
        </div>
        @php
            use Carbon\Carbon;
            use Carbon\CarbonPeriod;

            $weekdays = ['日', '月', '火', '水', '木', '金', '土'];
            $startOfMonth = $dateObj->copy()->startOfMonth();
            $endOfMonth = $dateObj->copy()->endOfMonth();
            $period = CarbonPeriod::create($startOfMonth, $endOfMonth);
        @endphp
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
                    $dateStr = $date->toDateString();
                    $attendance = $attendanceMap[$dateStr] ?? null;
                    $weekday = $weekdays[$date->dayOfWeek];
                    $startTime = $endTime = null;
                    $breakMin = $breakHour = $workMin = $workHour = null;

                    if ($attendance) {
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
                                    $start = Carbon::parse($break->break_start);
                                    $end = Carbon::parse($break->break_end);
                                    $totalBreakMinutes += $start->diffInMinutes($end);
                                }
                            }
                        }

                        $breakHour = floor($totalBreakMinutes / 60);
                        $breakMin = str_pad($totalBreakMinutes % 60, 2, '0', STR_PAD_LEFT);

                        if ($startTime && $endTime) {
                            $workMinutes = Carbon::parse($startTime)->diffInMinutes(Carbon::parse($endTime)) - $totalBreakMinutes;
                            $workHour = floor($workMinutes / 60);
                            $workMin = str_pad($workMinutes % 60, 2, '0', STR_PAD_LEFT);
                        }
                    }
                @endphp
                <tr>
                    <td><strong>{{ $date->format('m/d') }}({{ $weekday }})</strong></td>
                    <td>{{ $startTime ? Carbon::parse($startTime)->format('H:i') : '' }}</td>
                    <td>{{ $endTime ? Carbon::parse($endTime)->format('H:i') : '' }}</td>
                    <td>{{ isset($breakHour) && isset($breakMin) ? "$breakHour:$breakMin" : '' }}</td>
                    <td>{{ isset($workHour) && isset($workMin) ? "$workHour:$workMin" : '' }}</td>
                    <td>
                        @if ($attendance)
                            <a href="{{ route('attendance.show', ['id' => $attendance->id]) }}">詳細</a>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="button">
            <a class="button-submit" type="submit" href="{{ route('attendance.staff_export', ['id' => $user->id, 'year' => $dateObj->year, 'month' => $dateObj->month]) }}">CSV出力</a>
        </div>
    </div>
</div>
@endsection