<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\SalaryRequest;
use App\Models\LeaveRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function salaryRequests(Request $request)
    {
        $departments = Department::all();
        $query = SalaryRequest::with(['user', 'department'])
            ->orderBy('created_at', 'desc');

        // Filter by department if specified
        if ($request->has('department_id') && $request->department_id) {
            $query->where('department_id', $request->department_id);
        }

        // Filter by status if specified
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        // Filter by date range if specified
        if ($request->has('start_date') && $request->start_date) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->has('end_date') && $request->end_date) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        $requests = $query->paginate(15);

        return view('reports.salary-requests', compact('requests', 'departments'));
    }

    public function leaveRequests(Request $request)
    {
        $departments = Department::all();
        $query = LeaveRequest::with(['user', 'department'])
            ->orderBy('created_at', 'desc');

        // Filter by department if specified
        if ($request->has('department_id') && $request->department_id) {
            $query->where('department_id', $request->department_id);
        }

        // Filter by status if specified
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        // Filter by date range if specified
        if ($request->has('start_date') && $request->start_date) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->has('end_date') && $request->end_date) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        $requests = $query->paginate(15);

        return view('reports.leave-requests', compact('requests', 'departments'));
    }

    public function updateSalaryRequest(Request $request, SalaryRequest $salaryRequest)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,paid,rejected',
            'admin_notes' => 'nullable|string|max:1000',
        ]);

        DB::beginTransaction();
        try {
            $salaryRequest->update($validated);

            DB::commit();

            return response()->json([
                'message' => 'Salary request updated successfully',
                'request' => $salaryRequest->fresh()
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Error updating salary request'], 500);
        }
    }

    public function exportSalaryRequests(Request $request)
    {
        $requests = SalaryRequest::with(['user', 'department'])
            ->when($request->department_id, function($query) use ($request) {
                return $query->where('department_id', $request->department_id);
            })
            ->when($request->status, function($query) use ($request) {
                return $query->where('status', $request->status);
            })
            ->get();

        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=salary-requests.csv",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $callback = function() use ($requests) {
            $file = fopen('php://output', 'w');
            fputcsv($file, [
                'ID',
                'Employee',
                'Department',
                'Work Name',
                'Payment Method',
                'Status',
                'Admin Notes',
                'Created At'
            ]);

            foreach ($requests as $request) {
                fputcsv($file, [
                    $request->id,
                    $request->user->name,
                    $request->department->name,
                    $request->work_name,
                    $request->payment_method,
                    $request->status,
                    $request->admin_notes,
                    $request->created_at->format('Y-m-d H:i:s')
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
