<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Attendance</title>
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
    <link rel="stylesheet" href="{{ asset('css/header_nav.css') }}">
    @yield('css')
</head>

<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
@yield('script')
<body>
    <header class="header">
        <div class="header__inner">
            <a href="/">
                <img class="header__logo" src="{{ asset('images/logo.svg') }}" alt="COACHTECH">
            </a>
            @php
                $currentRouteName = Route::currentRouteName();
            @endphp
            @if (!in_array($currentRouteName, ['register', 'login']))
            <div class="header__nav">
                <a class="header__nav-item" href="/">勤怠</a>
                <a class="header__nav-item" href="/attendances/list">勤怠一覧</a>
                <a class="header__nav-item" href="/stamp_correction_request/list">申請</a>
                <form action="/logout" method="post">
                    @csrf
                    <button class="logout-form">ログアウト</button>
                </form>
            </div>
            @endif
        </div>
    </header>
    <main>
        @yield('content')
    </main>
</body>

</html>