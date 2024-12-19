<?php

namespace App\Http\Controllers;

use App\Models\LeaveRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LeaveRequestController extends Controller
{
    public function index()
    {
        try {
            $leaveRequests = LeaveRequest::where('user_id', Auth::id())
                ->orderBy('created_at', 'desc')
                ->get();

            return view('leave-requests.index', compact('leaveRequests'));
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Error loading leave requests: ' . $e->getMessage());
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'start_date' => 'required|date',
                'end_date' => 'required|date|after_or_equal:start_date',
                'leave_type' => 'required|in:annual,sick,unpaid,other',
                'description' => 'nullable|string|max:1000',
            ]);

            $leaveRequest = LeaveRequest::create([
                'user_id' => Auth::id(),
                'department_id' => Auth::user()->department_id,
                'start_date' => $validated['start_date'],
                'end_date' => $validated['end_date'],
                'leave_type' => $validated['leave_type'],
                'description' => $validated['description'],
                'status' => 'pending'
            ]);

            return redirect()
                ->route('leave-requests.index')
                ->with('success', 'Leave request submitted successfully.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Error creating leave request: ' . $e->getMessage());
        }
    }

    public function update(Request $request, LeaveRequest $leaveRequest)
    {
        try {
            // Verify ownership
            if ($leaveRequest->user_id !== Auth::id()) {
                abort(403, 'Unauthorized action.');
            }

            // Can only edit pending requests
            if ($leaveRequest->status !== 'pending') {
                return redirect()
                    ->route('leave-requests.index')
                    ->with('error', 'Can only edit pending requests.');
            }

            $validated = $request->validate([
                'start_date' => 'required|date',
                'end_date' => 'required|date|after_or_equal:start_date',
                'leave_type' => 'required|in:annual,sick,unpaid,other',
                'description' => 'nullable|string|max:1000',
            ]);

            $leaveRequest->update($validated);

            return redirect()
                ->route('leave-requests.index')
                ->with('success', 'Leave request updated successfully.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Error updating leave request: ' . $e->getMessage());
        }
    }
}
