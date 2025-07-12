@extends('layouts.admin')

@section('css')
<link rel="stylesheet" href="{{ asset('css/list.css') }}">
@endsection

@section('content')
<div class="content">
    <div class="content__inner">
        <h2>{{ \Carbon\Carbon::parse($date)->format('Y年n月j日') }}の勤怠</h2>
        @php
            $carbonDate = \Carbon\Carbon::parse($date);
            $prevDate = $carbonDate->copy()->subDay()->toDateString();
            $nextDate = $carbonDate->copy()->addDay()->toDateString();
        @endphp
        <div class="controls">
            <a class="nav-btn" href="{{ route('admin.attendance.list', ['date' => $prevDate]) }}">← 前日</a>
            <div class="current-month"><span class="calender-icon">📅</span> {{ \Carbon\Carbon::parse($date)->format('Y/m/d') }}</div>
            <a class="nav-btn" href="{{ route('admin.attendance.list', ['date' => $nextDate]) }}">翌日 →</a>
        </div>
        <table>
            <thead>
                <tr>
                    <th>名前</th>
                    <th>出勤</th>
                    <th>退勤</th>
                    <th>休憩</th>
                    <th>合計</th>
                    <th>詳細</th>
                </tr>
            </thead>
            <tbody>
                @foreach($attendances as $attendance)
                    @php
                        $correction = optional($attendance->approvedCorrectionRequest);

                        $startTime = $correction->requested_start_time ?? $attendance->start_time;
                        $endTime = $correction->requested_end_time ?? $attendance->end_time;

                        $totalBreakMinutes = 0;

                        if ($correction->requested_break1_start_time && $correction->requested_break1_end_time) {
                            $totalBreakMinutes += \Carbon\Carbon::parse($correction->requested_break1_start_time)
                                ->diffInMinutes(\Carbon\Carbon::parse($correction->requested_break1_end_time));
                        }
                        if ($correction->requested_break2_start_time && $correction->requested_break2_end_time) {
                            $totalBreakMinutes += \Carbon\Carbon::parse($correction->requested_break2_start_time)
                                ->diffInMinutes(\Carbon\Carbon::parse($correction->requested_break2_end_time));
                        }

                        if ($totalBreakMinutes === 0 && $attendance->breakTimes && $attendance->breakTimes->isNotEmpty()) {
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

                        $workMinutes = null;
                        if ($startTime && $endTime) {
                            $workMinutes = \Carbon\Carbon::parse($startTime)->diffInMinutes(\Carbon\Carbon::parse($endTime)) - $totalBreakMinutes;
                            $workHour = floor($workMinutes / 60);
                            $workMin = str_pad($workMinutes % 60, 2, '0', STR_PAD_LEFT);
                        }
                    @endphp
                <tr>
                    <td><strong>{{ $attendance->user->name }}</strong></td>
                    <td>{{ $startTime ? \Carbon\Carbon::parse($startTime)->format('H:i') : '-' }}</td>
                    <td>{{ $endTime ? \Carbon\Carbon::parse($endTime)->format('H:i') : '-' }}</td>
                    <td>{{ $totalBreakMinutes > 0 ? "$breakHour:$breakMin" : '-' }}</td>
                    <td>{{ $workMinutes !== null ? "$workHour:$workMin" : '-' }}</td>
                    <td><a href="{{ route('attendance.show', ['id' => $attendance->id]) }}">詳細</a></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection