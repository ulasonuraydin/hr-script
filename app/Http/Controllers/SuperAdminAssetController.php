<?php

namespace App\Http\Controllers;

use App\Models\CompanyAsset;
use App\Models\Department;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SuperAdminAssetController extends Controller
{
    public function index(Request $request)
    {
        $departments = Department::all();
        $query = CompanyAsset::with(['department', 'assignedUser'])
            ->orderBy('created_at', 'desc');

        // Filter by department
        if ($request->has('department_id') && $request->department_id) {
            $query->where('department_id', $request->department_id);
        }

        // Filter by type
        if ($request->has('type') && $request->type) {
            $query->where('type', $request->type);
        }

        // Filter by assignment status
        if ($request->has('assigned') && $request->assigned !== '') {
            if ($request->assigned) {
                $query->whereNotNull('assigned_to');
            } else {
                $query->whereNull('assigned_to');
            }
        }

        $assets = $query->paginate(15);

        return view('super-admin.assets.index', compact('assets', 'departments'));
    }

    public function create()
    {
        $departments = Department::all();
        return view('super-admin.assets.create', compact('departments'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:computer,phone,other',
            'serial_number' => 'nullable|string|max:255|unique:company_assets',
            'description' => 'nullable|string',
            'department_id' => 'required|exists:departments,id',
            'assigned_to' => 'nullable|exists:users,id',
            'assigned_date' => 'required_with:assigned_to|nullable|date'
        ]);

        DB::beginTransaction();
        try {
            $asset = CompanyAsset::create($validated);
            DB::commit();

            return redirect()
                ->route('super-admin.assets.index')
                ->with('success', 'Asset created successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error creating asset: ' . $e->getMessage());
        }
    }

    public function edit(CompanyAsset $asset)
    {
        $departments = Department::all();
        $users = User::where('department_id', $asset->department_id)->get();
        return view('super-admin.assets.edit', compact('asset', 'departments', 'users'));
    }

    public function update(Request $request, CompanyAsset $asset)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:computer,phone,other',
            'serial_number' => 'nullable|string|max:255|unique:company_assets,serial_number,' . $asset->id,
            'description' => 'nullable|string',
            'department_id' => 'required|exists:departments,id',
            'assigned_to' => 'nullable|exists:users,id',
            'assigned_date' => 'required_with:assigned_to|nullable|date'
        ]);

        DB::beginTransaction();
        try {
            $asset->update($validated);
            DB::commit();

            return redirect()
                ->route('super-admin.assets.index')
                ->with('success', 'Asset updated successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error updating asset: ' . $e->getMessage());
        }
    }

    public function destroy(CompanyAsset $asset)
    {
        try {
            $asset->delete();
            return response()->json(['message' => 'Asset deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error deleting asset'], 500);
        }
    }

    public function getDepartmentUsers(Department $department)
    {
        $users = $department->users()->select('id', 'name')->get();
        return response()->json($users);
    }
}
