<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\ProjectOwner;
use Illuminate\Support\Facades\Hash;
use App\Services\HomeService;

class ProjectOwnerAuthController extends Controller
{
    public function __construct(HomeService $homeService)
    {
        parent::__construct($homeService);
    }

    public function showLoginForm()
    {
        return view('auth.project_owners.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');
        if (Auth::guard('project_owner')->attempt($credentials)) {
            return redirect()->intended('/project-owner/dashboard');
        }
        return back()->withErrors(['email' => 'Invalid credentials']);
    }

    public function showRegisterForm()
    {
        return view('auth.project_owners.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:project_owners,email',
            'password' => 'required|confirmed|min:6',
        ]);

        ProjectOwner::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('project_owner.login')->with('success', 'Registered successfully.');
    }

    public function logout()
    {
        Auth::guard('project_owner')->logout();
        return redirect()->route('project_owner.login');
    }
    public function dashboard()
    {
        return view('auth.project_owners.dashboard');
    }
}
