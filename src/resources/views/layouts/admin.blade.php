<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance</title>
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
    <link rel="stylesheet" href="{{ asset('css/header_nav.css') }}">
    @yield('css')
</head>

<body>
    <header class="header">
        <div class="header__inner">
            <a href="/">
                <img class="header__logo" src="{{ asset('images/logo.svg') }}" alt="COACHTECH">
            </a>
            @php
                $currentRouteName = Route::currentRouteName();
            @endphp
            @if (!in_array($currentRouteName, ['admin.login']))
            <div class="header__nav">
                <a class="header__nav-item" href="{{ route('admin.attendance.list', ['date' => now()->toDateString()]) }}">勤怠一覧</a>
                <a class="header__nav-item" href="/admin/staff/list">スタッフ一覧</a>
                <a class="header__nav-item" href="/stamp_correction_request/list">申請一覧</a>
                <form action="{{ route('logout') }}" method="post">
                    @csrf
                    <button class="logout-form" type="submit">ログアウト</button>
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