<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\DB;

class DepartmentEmployeeController extends Controller
{
    public function index()
    {
        $employees = User::where('department_id', auth()->user()->department_id)
            ->where('role', 'employee')
            ->orderBy('name')
            ->get();

        return view('department.employees.index', compact('employees'));
    }

    public function edit(User $employee)
    {
        // Verify the employee belongs to the admin's department
        if ($employee->department_id !== auth()->user()->department_id) {
            abort(403, 'Unauthorized action.');
        }

        // Verify the employee is not an admin
        if ($employee->role !== 'employee') {
            abort(403, 'Can only edit employee profiles.');
        }

        return view('department.employees.edit', compact('employee'));
    }

    public function update(Request $request, User $employee)
    {
        // Verify the employee belongs to the admin's department
        if ($employee->department_id !== auth()->user()->department_id) {
            abort(403, 'Unauthorized action.');
        }

        // Verify the employee is not an admin
        if ($employee->role !== 'employee') {
            abort(403, 'Can only edit employee profiles.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $employee->id],
            'password' => ['nullable', 'confirmed', Password::defaults()],
            'phone' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:255'],
            'emergency_contact' => ['nullable', 'string', 'max:255'],
            'emergency_phone' => ['nullable', 'string', 'max:20'],
            'joining_date' => ['nullable', 'date'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        DB::beginTransaction();
        try {
            // Only update password if provided
            if (isset($validated['password'])) {
                $validated['password'] = Hash::make($validated['password']);
            } else {
                unset($validated['password']);
            }

            $employee->update($validated);

            // Log the update
            activity()
                ->performedOn($employee)
                ->causedBy(auth()->user())
                ->log('employee_profile_updated');

            DB::commit();

            return redirect()
                ->route('department.employees.index')
                ->with('success', 'Employee profile updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Error updating employee profile: ' . $e->getMessage());
        }
    }

    public function show(User $employee)
    {
        // Verify the employee belongs to the admin's department
        if ($employee->department_id !== auth()->user()->department_id) {
            abort(403, 'Unauthorized action.');
        }

        // Load related data
        $employee->load([
            'workSchedules' => function ($query) {
                $query->where('is_template', false)
                    ->orderBy('week_start', 'desc');
            },
            'leaveRequests' => function ($query) {
                $query->orderBy('created_at', 'desc');
            },
            'salaryRequests' => function ($query) {
                $query->orderBy('created_at', 'desc');
            },
            'assignedAssets'
        ]);

        return view('department.employees.show', compact('employee'));
    }
}
