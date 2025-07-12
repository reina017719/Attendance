<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class AdminAuthController extends Controller
{
    public function adminLogin()
    {
        return view('admin.auth.login');
    }

    public function loginForm(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'min:8'],
        ], [
            'email.required' => 'メールアドレスを入力してください',
            'email.email' => 'メールアドレス形式で入力してください',
            'password.required' => 'パスワードを入力してください',
            'password.min' => '8文字以上で入力して下さい',
        ]);

        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            if (Auth::user()->role !== 'admin') {
                Auth::logout();
                return back()->withErrors(['email' => '管理者権限がありません']);
            }
            return redirect('/admin/attendance/list');
        }
        return back()->withErrors(['email' => 'ログイン情報が正しくありません'])->withInput();
    }
}