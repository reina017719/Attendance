@extends('layouts.user')

@section('css')
<link rel="stylesheet" href="{{ asset('css/register.css') }}">
@endsection

@section('content')
<div class="register-content">
    <div class="register__heading">
        <h2>会員登録</h2>
    </div>
    <form class="form" action="/register" method="post">
        @csrf
        <div class="form__group">
            <div class="form__group-title">
                <label class="form__label">ユーザー名</label>
            </div>
            <div class="form__group-content">
                <div class="form__input">
                    <input type="text" name="name" value="{{ old('name') }}" />
                </div>
                @error('name')
                    <p class="form__error" style="color:red;">{{ $message }}</p>
                @enderror
            </div>
        </div>
        <div class="form__group">
            <div class="form__group-title">
                <label class="form__label">メールアドレス</label>
            </div>
            <div class="form__group-content">
                <div class="form__input">
                    <input type="email" name="email" value="{{ old('email') }}" />
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
        <div class="form__group">
            <div class="form__group-title">
                <label class="form__label">確認用パスワード</label>
            </div>
            <div class="form__group-content">
                <div class="form__input">
                    <input type="password" name="password_confirmation">
                </div>
                @error('password_confirmation')
                    <p class="form__error" style="color:red;">{{ $message }}</p>
                @enderror
            </div>
        </div>
        <div class="form__button">
            <button class="form__button-submit" type="submit">登録する</button>
            <div class="login__button">
                <a class="login__button-submit" href="/login">ログインはこちら</a>
            </div>
        </div>
    </form>
</div>
@endsection