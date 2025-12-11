<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends BaseController
{
    public function showLogin(Request $request)
    {
        return view('admin.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $remember = $request->filled('remember'); // true if checkbox checked

        if (Auth::guard('administration')->attempt(
            $request->only('email', 'password'),
            $remember
        )) {
            $user = Auth::guard('administration')->user();

            if ($user && $user->user_type === 'Admin') {
                return redirect()->intended('/admin/dashboard');
            }

            Auth::guard('administration')->logout();
            return redirect('/administration')->with('error', 'Unauthorized access.');
        }

        return redirect('/administration')->with('error', 'Invalid credentials.');
    }


    public function logout(Request $request)
    {
        Auth::guard('administration')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/administration');
    }
}
