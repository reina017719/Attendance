<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Requests\RegisterRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function register(RegisterRequest $request)
    {
        $user_data = $request->only(['name', 'email', 'password']);
        $user_data['password'] = Hash::make($user_data['password']);

        $user = User::create($user_data);
        Auth::login($user);

        return redirect('/');
    }
}