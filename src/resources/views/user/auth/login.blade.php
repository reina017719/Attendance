@extends('layouts.user')

@section('css')
<link rel="stylesheet" href="{{ asset('css/login.css') }}">
@endsection

@section('content')
<div class="login-content">
    <div class="login__heading">
        <h2>ログイン</h2>
    </div>
    <form class="form" action="/login" method="post">
        @csrf
        <div class="form__group">
            <div class="form__group-title">
                <label class="form__label">メールアドレス</label>
            </div>
            <div class="form__group-content">
                <div class="form__input">
                    <input type="text" name="email" value="{{ old('email') }}" />
                </div>
                @error('email')
                    <p class="form__error" style="color:red;">{{ $message }}</p>
                @enderror
            </div>
        </div>
        <div class="form__group">
            <div class="form__group-title">
                <label class="form__label">パスワード</label>
            </div>
            <div class="form__group-content">
                <div class="form__input">
                    <input type="password" name="password">
                </div>
                @error('password')
                    <p class="form__error" style="color:red;">{{ $message }}</p>
                @enderror
            </div>
        </div>
        <div class="form__button">
            <button class="form__button-submit" type="submit">ログインする</button>
            <div class="login__button">
                <a class="login__button-submit" href="/register">会員登録はこちら</a>
            </div>
        </div>
    </form>
</div>
@endsection