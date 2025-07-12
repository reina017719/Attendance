@extends('layouts.admin')

@section('css')
<link rel="stylesheet" href="{{ asset('css/show.css') }}">
@endsection

@section('content')
<div class="content">
    <div class="content__inner">
        <h2>勤怠詳細</h2>
        <div class="card">
            <div class="row">
            <div class="label">名前</div>
            <div class="input-area">{{ $request->user->name }}</div>
        </div>
        <div class="row">
            <div class="label">日付</div>
            @php
                $date = \Carbon\Carbon::parse($request->attendance->work_date);
            @endphp
            <div class="year">{{ $date->year }}</div>
            <span class="date">{{ $date->format('n月j日') }}</span>
        </div>
        <div class="row">
            <div class="label">出勤・退勤</div>
            <div class="input-area">
                <input class="time__input" value="{{ $request->requested_start_time ? \Carbon\Carbon::parse($request->requested_start_time)->format('H:i') : '' }}">
                <span class="tilde">〜</span>
                <input class="time__input" value="{{ $request->requested_end_time ? \Carbon\Carbon::parse($request->requested_end_time)->format('H:i') : '' }}">
            </div>
        </div>
        <div class="row">
            <div class="label">休憩</div>
            <div class="input-area">
                <input class="time__input" value="{{ $request->requested_break1_start_time ? \Carbon\Carbon::parse($request->requested_break1_start_time)->format('H:i') : '' }}">
                <span class="tilde">〜</span>
                <input class="time__input" value="{{ $request->requested_break1_end_time ? \Carbon\Carbon::parse($request->requested_break1_end_time)->format('H:i') : '' }}">
            </div>
        </div>
        <div class="row">
            <div class="label">休憩2</div>
            <div class="input-area">
                <input class="time__input" value="{{ $request->requested_break2_start_time ? \Carbon\Carbon::parse($request->requested_break2_start_time)->format('H:i') : '' }}">
                <span class="tilde">〜</span>
                <input class="time__input" value="{{ $request->requested_break2_end_time ? \Carbon\Carbon::parse($request->requested_break2_end_time)->format('H:i') : '' }}">
            </div>
        </div>
        <div class="row">
            <div class="label">備考</div>
            <div class="input-area">{{ $request->reason }}</div>
        </div>
    </div>
    <div class="button-container">
        @if ($request->status === 'approved')
            <button type="button" disabled style="background-color: #696969">承認済み</button>
        @else
            <form action="" method="post">
                @csrf
                <button type="submit">承認</button>
            </form>
        @endif
    </div>
</div>
@endsection