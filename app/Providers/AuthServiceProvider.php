<?php
namespace App\Providers;

use App\Models\CompanyAsset;
use App\Models\LeaveRequest;
use App\Models\SalaryRequest;
use App\Models\User;
use App\Models\WorkSchedule;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // User Management
        Gate::define('manage-users', function (User $user) {
            return $user->isSuperAdmin();
        });

        // Department Management
        Gate::define('manage-department', function (User $user) {
            return in_array($user->role, ['department_admin', 'super_admin']);
        });

        // Salary Requests
        Gate::define('view-salary-requests', function (User $user, ?int $departmentId = null) {
            if ($user->isSuperAdmin()) {
                return true;
            }
            if ($user->role === 'department_admin') {
                return $user->department_id === $departmentId;
            }
            return true; // Employees can view their own (filtered in controller)
        });

        Gate::define('update-salary-request', function (User $user, SalaryRequest $request) {
            if ($user->isSuperAdmin()) {
                return true;
            }
            return $user->id === $request->user_id;
        });

        // Leave Requests
        Gate::define('view-leave-requests', function (User $user, ?int $departmentId = null) {
            if ($user->isSuperAdmin()) {
                return true;
            }
            if ($user->role === 'department_admin') {
                return $user->department_id === $departmentId;
            }
            return true; // Employees can view their own (filtered in controller)
        });

        Gate::define('update-leave-request', function (User $user, LeaveRequest $request) {
            if ($user->isSuperAdmin()) {
                return true;
            }
            return $user->id === $request->user_id;
        });

        // Work Schedules
        Gate::define('manage-schedules', function (User $user, ?int $departmentId = null) {
            if ($user->isSuperAdmin()) {
                return true;
            }
            if ($user->role === 'department_admin') {
                return $user->department_id === $departmentId;
            }
            return false;
        });

        // Department Leave Management
        Gate::define('manage-department-leaves', function (User $user) {
            return in_array($user->role, ['department_admin', 'super_admin']);
        });

        Gate::define('update-department-leave', function (User $user, LeaveRequest $request) {
            if ($user->isSuperAdmin()) {
                return true;
            }
            if ($user->role === 'department_admin') {
                return $user->department_id === $request->department_id;
            }
            return false;
        });

        // Company Assets
        Gate::define('manage-assets', function (User $user, ?int $departmentId = null) {
            if ($user->isSuperAdmin()) {
                return true;
            }
            if ($user->role === 'department_admin') {
                return $user->department_id === $departmentId;
            }
            return false;
        });
    }
}
