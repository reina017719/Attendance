@php
    $layout = Auth::user()->role === 'admin' ? 'layouts.admin' : 'layouts.user';
@endphp
@extends($layout)

@section('css')
<link rel="stylesheet" href="{{ asset('css/show.css') }}">
@endsection

@section('content')
<div class="content">
    @php
        $isReadOnly = isset($pendingRequest) && $pendingRequest->status === 'pending';
        $startTime = $pendingRequest->requested_start_time ?? $attendance->start_time;
        $endTime = $pendingRequest->requested_end_time ?? $attendance->end_time;
        $break1_start = $pendingRequest->requested_break1_start_time ?? ($attendance->breakTimes[0]->break_start ?? '');
        $break1_end = $pendingRequest->requested_break1_end_time ?? ($attendance->breakTimes[0]->break_end ?? '');
        $break2_start = $pendingRequest->requested_break2_start_time ?? ($attendance->breakTimes[1]->break_start ?? '');
        $break2_end = $pendingRequest->requested_break2_end_time ?? ($attendance->breakTimes[1]->break_end ?? '');
        $reason = $pendingRequest->reason ?? '';
    @endphp
    <form action="{{ route('corrections.store') }}" method="post">
        @csrf
        <input type="hidden" name="attendance_id" value="{{ $attendance->id}}">

        <div class="content__inner">
            <h2>勤怠詳細</h2>
            <div class="card">
                <div class="row">
                    <div class="label">名前</div>
                    <div class="input-area">{{ $attendance->user->name }}</div>
                </div>
                <div class="row">
                    <div class="label">日付</div>
                    <div class="input-area">{{ \Carbon\Carbon::parse($attendance->work_date)->format('Y年') }}</div>
                    <span class="input-date">{{ \Carbon\Carbon::parse($attendance->work_date)->format('n月j日') }}</span>
                </div>
                <div class="row">
                    <div class="label">出勤・退勤</div>
                    <div class="input-area">
                    <input class="time" type="time" name="requested_start_time" value="{{ $startTime ? \Carbon\Carbon::parse($startTime)->format('H:i') : '' }}" @if ($isReadOnly) readonly @endif>
                    <span class="tilde">〜</span>
                    <input class="time" type="time" name="requested_end_time" value="{{ $endTime ? \Carbon\Carbon::parse($endTime)->format('H:i') : '' }}" @if ($isReadOnly) readonly @endif>
                </div>
                @error('requested_start_time')
                    <p class="form__error" style="color:red;">{{ $message }}</p>
                @enderror
                @error('requested_end_time')
                    <p class="form__error" style="color:red;">{{ $message }}</p>
                @enderror
            </div>
            @php
                $breaks = $attendance->breakTimes;
                $maxBreaks = max(2, $breaks->count());
            @endphp

            @for ($i = 0; $i < $maxBreaks; $i++)
                @php
                    $start = '';
                    $end = '';
                    if ($i === 0) {
                        $start = $break1_start;
                        $end = $break1_end;
                    } elseif ($i === 1) {
                        $start = $break2_start;
                        $end = $break2_end;
                    }
                    $startFormatted = $start ? \Carbon\Carbon::parse($start)->format('H:i') : '';
                    $endFormatted = $end ? \Carbon\Carbon::parse($end)->format('H:i') : '';
                @endphp
                <div class="row">
                    <div class="label">休憩{{ $i + 1}}</div>
                    <div class="input-area">
                        <input class="time" type="time" name="requested_break{{ $i + 1 }}_start_time" value="{{ $startFormatted }}" @if ($isReadOnly) readonly @endif>
                        <span class="tilde">〜</span>
                        <input class="time" type="time" name="requested_break{{ $i + 1 }}_end_time" value="{{ $endFormatted }}" @if ($isReadOnly) readonly @endif>
                    </div>
                </div>
            @endfor
            <div class="row">
                <div class="label">備考</div>
                <div class="input-area">
                    <textarea name="reason" placeholder="電車遅延のため" @if ($isReadOnly) readonly @endif>{{ $reason }}</textarea>
                </div>
                @error('reason')
                    <p class="form__error" style="color:red;">{{ $message }}</p>
                @enderror
            </div>
        </div>
        <div class="button-container">
            @if (isset($pendingRequest) && $pendingRequest->status === 'pending')
                <p class="form__note" style="color:red;">＊承認待ちのため修正はできません</p>
            @else
            <button type="submit">修正</button>
            @endif
        </div>
    </form>
</div>
@endsection