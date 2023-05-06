<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Route;
use Auth;
use App\Admin;
use Illuminate\Support\Facades\Hash;

class AdminLoginController extends Controller
{
	public function __construct()
    {
        $this->middleware('guest:admin', ['except' => ['logout']]);
    }

    public function showLoginForm()
    {
        return view('admin.auth.adminlogin');
    }

    public function login(Request $request)
    {
        // Validate the form data
        $this->validate($request, [
            'username' => 'required|min:4',
            'password' => 'required|string'
        ]);

        if (Auth::guard('admin')->attempt(['username' => $request->username, 'password' => $request->password], $request->remember)) 
        {
            // if successful, then redirect to their intended location
            return redirect()->intended(route('admin.dashboard'));
        }
    
      // if unsuccessful, then redirect back to the login with the form data
        return redirect()->back()->withInput($request->only('username','remember'))->with('warning', 'Incorrect username or password!');
    }

    public function logout(Request $request)
    {
        Auth::guard('admin')->logout();

        $request->session()->invalidate();

        return $this->loggedOut($request) ?: redirect('/admin/login');
    }

    protected function loggedOut(Request $request)
    {
        
    }
}
