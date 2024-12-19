<?php

namespace App\Http\Controllers;

use App\Models\SalaryRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SalaryRequestController extends Controller
{
    // Remove the constructor since we're handling middleware in the routes file

    public function index()
    {
        try {
            $salaryRequests = SalaryRequest::where('user_id', Auth::id())
                ->orderBy('created_at', 'desc')
                ->get();

            return view('salary-requests.index', compact('salaryRequests'));
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Error loading salary requests: ' . $e->getMessage());
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'work_name' => 'required|string|max:255',
                'payment_method' => 'required|in:bank_transfer,tether_trc',
                'wallet_address' => 'required_if:payment_method,tether_trc|nullable|string|max:255',
            ]);

            $salaryRequest = SalaryRequest::create([
                'user_id' => Auth::id(),
                'department_id' => Auth::user()->department_id,
                'work_name' => $validated['work_name'],
                'payment_method' => $validated['payment_method'],
                'wallet_address' => $validated['wallet_address'] ?? null,
                'status' => 'pending'
            ]);

            return redirect()
                ->route('salary-requests.index')
                ->with('success', 'Salary request submitted successfully.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Error creating salary request: ' . $e->getMessage());
        }
    }

    public function update(Request $request, SalaryRequest $salaryRequest)
    {
        try {
            // Verify ownership
            if ($salaryRequest->user_id !== Auth::id()) {
                abort(403, 'Unauthorized action.');
            }

            // Can only edit pending requests
            if ($salaryRequest->status !== 'pending') {
                return redirect()
                    ->route('salary-requests.index')
                    ->with('error', 'Can only edit pending requests.');
            }

            $validated = $request->validate([
                'work_name' => 'required|string|max:255',
                'payment_method' => 'required|in:bank_transfer,tether_trc',
                'wallet_address' => 'required_if:payment_method,tether_trc|nullable|string|max:255',
            ]);

            $salaryRequest->update($validated);

            return redirect()
                ->route('salary-requests.index')
                ->with('success', 'Salary request updated successfully.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Error updating salary request: ' . $e->getMessage());
        }
    }
}
