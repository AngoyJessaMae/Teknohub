<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Customer;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->intended(route('dashboard'));
        }

        return back()->withErrors([
            'email' => 'Invalid credentials.',
        ]);
    }

    public function showRegisterForm()
    {
        if (Auth::check()) {
            Auth::logout();
            session()->invalidate();
            session()->regenerateToken();
        }
        
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'contact_number' => 'required|string|max:20',
            'password' => 'required|min:8|confirmed',
            'role' => 'required|in:Customer,Employee',
        ]);

        $user = User::create([
            'full_name' => $validated['full_name'],
            'email' => $validated['email'],
            'contact_number' => $validated['contact_number'],
            'password' => $validated['password'],
            'role' => $validated['role'],
            'account_status' => 'Active',
        ]);

        // create acc based on role
        if ($validated['role'] === 'Customer') {
            Customer::create([
                'user_id' => $user->user_id,
            ]);
        } elseif ($validated['role'] === 'Employee') {
            Employee::create([
                'user_id' => $user->user_id,
                'department_name' => $request->department_name ?? null,
                'job_title' => $request->job_title ?? null,
            ]);
        }

        Auth::login($user);

        $request->session()->regenerate();

        return redirect()->route('dashboard');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
}