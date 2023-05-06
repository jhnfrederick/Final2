<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('login');
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        $user = User::where('email', $credentials['email'])->first();
        
        if ($user && $credentials['password'] === $user->password) {
    session(['user_id' => $user->id]);
    return redirect()->route('dashboard');
}


        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ]);
    }

    public function logout(Request $request)
    {
        $request->session()->forget('user_id');
        return redirect()->route('login');
    }
}
