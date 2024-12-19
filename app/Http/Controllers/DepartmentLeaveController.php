<?php

namespace App\Http\Controllers;

use App\Models\LeaveRequest;
use App\Models\Department;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DepartmentLeaveController extends Controller
{
    public function index(Request $request)
    {
        $department = auth()->user()->department;
        $query = LeaveRequest::with(['user', 'department'])
            ->where('department_id', $department->id)
            ->orderBy('created_at', 'desc');

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

        return view('department.leave-requests.index', compact('requests'));
    }

    public function update(Request $request, LeaveRequest $leaveRequest)
    {
        // Verify department admin owns this department
        if ($leaveRequest->department_id !== auth()->user()->department_id) {
            return response()->json(['error' => 'Unauthorized action.'], 403);
        }

        try {
            $validated = $request->validate([
                'status' => 'required|in:pending,approved,rejected',
                'admin_notes' => 'nullable|string|max:1000',
            ]);

            DB::beginTransaction();

            $leaveRequest->update($validated);

            DB::commit();

            return response()->json([
                'message' => 'Leave request updated successfully',
                'request' => $leaveRequest->fresh()
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return response()->json(['error' => $e->errors()], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Error updating leave request'], 500);
        }
    }

    public function export(Request $request)
    {
        $department = auth()->user()->department;
        $query = LeaveRequest::with(['user', 'department'])
            ->where('department_id', $department->id)
            ->orderBy('created_at', 'desc');

        // Apply filters
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }
        if ($request->has('start_date') && $request->start_date) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->has('end_date') && $request->end_date) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        $requests = $query->get();

        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=department-leave-requests.csv",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $callback = function() use ($requests) {
            $file = fopen('php://output', 'w');
            fputcsv($file, [
                'ID',
                'Employee',
                'Leave Type',
                'Start Date',
                'End Date',
                'Status',
                'Admin Notes',
                'Created At'
            ]);

            foreach ($requests as $request) {
                fputcsv($file, [
                    $request->id,
                    $request->user->name,
                    $request->leave_type,
                    $request->start_date->format('Y-m-d'),
                    $request->end_date->format('Y-m-d'),
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
