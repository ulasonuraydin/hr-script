<?php
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\LeaveRequestController;
use App\Http\Controllers\SalaryRequestController;
use App\Http\Controllers\WorkScheduleController;
use App\Http\Controllers\DepartmentScheduleController;
use App\Http\Controllers\DepartmentLeaveController;
use App\Http\Controllers\DepartmentSalaryController;
use App\Http\Controllers\CompanyAssetController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\DepartmentEmployeeController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SuperAdminAssetController;
use Illuminate\Support\Facades\Route;

// Guest routes (login/register) should be outside the auth middleware
Route::middleware('guest')->group(function () {
    Route::get('/', function () {
        return redirect()->route('login');
    });
    Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('login', [LoginController::class, 'login']);
    Route::get('register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('register', [RegisterController::class, 'register']);
});

// Protected routes that require authentication
Route::middleware('auth')->group(function () {
    // Logout route
    Route::post('logout', [LoginController::class, 'logout'])->name('logout');

    // Dashboard route
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // Employee Routes
    Route::middleware('role:employee')->group(function () {
        Route::resource('salary-requests', SalaryRequestController::class)
            ->only(['index', 'store', 'update']);
        Route::resource('leave-requests', LeaveRequestController::class)
            ->only(['index', 'store', 'update']);
        Route::get('work-schedule', [WorkScheduleController::class, 'mySchedule'])
            ->name('work-schedule');
        Route::get('work-schedule/events', [WorkScheduleController::class, 'events'])
            ->name('work-schedule.events');
    });

    // Department Admin Routes
    Route::middleware('role:department_admin')->group(function () {
        // Department Schedule Management
        Route::get('department/schedules', [DepartmentScheduleController::class, 'index'])
            ->name('department.schedules.index');
        Route::post('department/schedules', [DepartmentScheduleController::class, 'store'])
            ->name('department.schedules.store');
        Route::post('department/schedules/template', [DepartmentScheduleController::class, 'createTemplate'])
            ->name('department.schedules.create-template');
        Route::post('department/schedules/bulk-assign', [DepartmentScheduleController::class, 'bulkAssign'])
            ->name('department.schedules.bulk-assign');
        Route::get('department/schedules/view', [DepartmentScheduleController::class, 'viewDepartmentSchedule'])
            ->name('department.schedules.view');
        Route::delete('department/schedules/{schedule}', [DepartmentScheduleController::class, 'destroy'])
            ->name('department.schedules.destroy');

        // Department Leave Requests
        Route::get('department/leave-requests', [DepartmentLeaveController::class, 'index'])
            ->name('department.leave-requests');
        Route::put('department/leave-requests/{leaveRequest}', [DepartmentLeaveController::class, 'update'])
            ->name('department.leave-requests.update');
        Route::get('department/leave-requests/export', [DepartmentLeaveController::class, 'export'])
            ->name('department.leave-requests.export');

        // Department Employee Management
        Route::get('department/employees', [DepartmentEmployeeController::class, 'index'])
            ->name('department.employees.index');
        Route::get('department/employees/{employee}', [DepartmentEmployeeController::class, 'show'])
            ->name('department.employees.show');
        Route::get('department/employees/{employee}/edit', [DepartmentEmployeeController::class, 'edit'])
            ->name('department.employees.edit');
        Route::put('department/employees/{employee}', [DepartmentEmployeeController::class, 'update'])
            ->name('department.employees.update');

        // Department Assets
        Route::resource('department/assets', CompanyAssetController::class)
            ->names([
                'index' => 'department.assets.index',
                'create' => 'department.assets.create',
                'store' => 'department.assets.store',
                'edit' => 'department.assets.edit',
                'update' => 'department.assets.update',
                'destroy' => 'department.assets.destroy',
            ]);

        // Department Salary Requests
        Route::get('department/salary-requests', [DepartmentSalaryController::class, 'index'])
            ->name('department.salary-requests');
        Route::put('department/salary-requests/{salaryRequest}', [DepartmentSalaryController::class, 'update'])
            ->name('department.salary-requests.update');
        Route::get('department/salary-requests/export', [DepartmentSalaryController::class, 'export'])
            ->name('department.salary-requests.export');

    });

    // Super Admin Routes
    Route::middleware('role:super_admin')->group(function () {
        Route::resource('users', UserController::class);
        Route::resource('departments', DepartmentController::class);
        Route::get('reports/salary-requests', [ReportController::class, 'salaryRequests'])
            ->name('reports.salary-requests');
        Route::put('reports/salary-requests/{salaryRequest}', [ReportController::class, 'updateSalaryRequest'])
            ->name('reports.salary-requests.update');
        Route::get('reports/salary-requests/export', [ReportController::class, 'exportSalaryRequests'])
            ->name('reports.salary-requests.export');
        Route::get('reports/leave-requests', [ReportController::class, 'leaveRequests'])
            ->name('reports.leave-requests');
        Route::resource('super-admin/assets', SuperAdminAssetController::class, [
            'as' => 'super-admin'
        ]);
        Route::get('super-admin/department/{department}/users', [SuperAdminAssetController::class, 'getDepartmentUsers'])
            ->name('super-admin.department.users');
    });
});
