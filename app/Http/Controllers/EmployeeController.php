<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class EmployeeController extends Controller
{
    public function index()
    {
        $this->ensureAdmin();

        $employees = Employee::with('user')->latest()->get();
        return view('employees.index', compact('employees'));
    }

    public function create()
    {
        $this->ensureAdmin();

        return view('employees.create');
    }

    public function store(Request $request)
    {
        $this->ensureAdmin();

        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'contact_number' => 'required|string|max:20',
            'department_name' => 'required|string|max:255',
            'job_title' => 'required|string|max:255',
            'skills' => 'nullable|string',
        ]);

        $generatedPassword = Str::random(10);

        $employee = null;

        DB::transaction(function () use ($validated, $generatedPassword, &$employee) {
            $user = User::create([
                'full_name' => $validated['full_name'],
                'email' => $validated['email'],
                'contact_number' => $validated['contact_number'],
                'password' => Hash::make($generatedPassword),
                'role' => 'Employee',
                'account_status' => 'Active',
            ]);

            $employee = Employee::create([
                'user_id' => $user->user_id,
                'department_name' => $validated['department_name'],
                'job_title' => $validated['job_title'],
                'skills' => $validated['skills'] ?? null,
            ]);
        });

        return redirect()
            ->route('employees.index')
            ->with('success', "Employee created successfully. Email: {$validated['email']} | Password: {$generatedPassword}");
    }

    public function edit(Employee $employee)
    {
        $this->ensureAdmin();

        $employee->load('user');
        return view('employees.edit', compact('employee'));
    }

    public function update(Request $request, Employee $employee)
    {
        $this->ensureAdmin();

        $employee->load('user');

        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                Rule::unique('users', 'email')->ignore($employee->user_id, 'user_id'),
            ],
            'contact_number' => 'required|string|max:20',
            'department_name' => 'required|string|max:255',
            'job_title' => 'required|string|max:255',
            'skills' => 'nullable|string',
        ]);

        DB::transaction(function () use ($employee, $validated) {
            $employee->user->update([
                'full_name' => $validated['full_name'],
                'email' => $validated['email'],
                'contact_number' => $validated['contact_number'],
            ]);

            $employee->update([
                'department_name' => $validated['department_name'],
                'job_title' => $validated['job_title'],
                'skills' => $validated['skills'] ?? null,
            ]);
        });

        return redirect()
            ->route('employees.index')
            ->with('success', 'Employee updated successfully.');
    }

    public function destroy(Employee $employee)
    {
        $this->ensureAdmin();

        DB::transaction(function () use ($employee) {
            $employee->delete();
            $employee->user()->delete();
        });

        return redirect()
            ->route('employees.index')
            ->with('success', 'Employee deleted successfully.');
    }

    private function ensureAdmin(): void
    {
        if (!auth()->check() || auth()->user()->role !== 'Admin') {
            abort(403, 'Only administrators can manage employees.');
        }
    }
}