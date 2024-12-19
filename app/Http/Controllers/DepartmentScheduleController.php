<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\WorkSchedule;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class DepartmentScheduleController extends Controller
{
    public function index()
    {
        $department = auth()->user()->department;
        $employees = $department->users()
            ->where('role', 'employee')
            ->select('id', 'name')
            ->get();

        $templates = WorkSchedule::where('department_id', $department->id)
            ->where('is_template', true)
            ->get();

        // Get current week's schedules for all department employees
        $currentWeekStart = Carbon::now()->startOfWeek();
        $currentWeekEnd = Carbon::now()->endOfWeek();

        $schedules = WorkSchedule::with('user')
            ->where('department_id', $department->id)
            ->where('is_template', false)
            ->whereBetween('week_start', [$currentWeekStart, $currentWeekEnd])
            ->get();

        return view('department.schedules.index', compact('employees', 'templates', 'schedules'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'shift_name' => 'required|string|max:255',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i',
            'working_days' => 'required|array',
            'working_days.*' => 'required|integer|between:0,6',
            'week_start' => 'required|date',
            'is_template' => 'boolean'
        ]);

        $validated['working_days'] = array_map('intval', $validated['working_days']);

        $weekStart = Carbon::parse($validated['week_start'])->startOfWeek();
        $weekEnd = $weekStart->copy()->endOfWeek();

        $schedule = WorkSchedule::create([
            'user_id' => $validated['user_id'],
            'department_id' => auth()->user()->department_id,
            'shift_name' => $validated['shift_name'],
            'start_time' => $validated['start_time'],
            'end_time' => $validated['end_time'],
            'working_days' => $validated['working_days'],
            'week_start' => $weekStart,
            'week_end' => $weekEnd,
            'is_template' => $validated['is_template'] ?? false
        ]);

        return redirect()
            ->route('department.schedules.index')
            ->with('success', 'Schedule created successfully');
    }

    public function createTemplate(Request $request)
    {
        $validated = $request->validate([
            'shift_name' => 'required|string|max:255',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i',
            'working_days' => 'required|array',
            'working_days.*' => 'required|integer|between:0,6'
        ]);

        try {
            WorkSchedule::create([
                'department_id' => auth()->user()->department_id,
                'shift_name' => $validated['shift_name'],
                'start_time' => $validated['start_time'],
                'end_time' => $validated['end_time'],
                'working_days' => $validated['working_days'],
                'is_template' => true
            ]);

            return redirect()
                ->route('department.schedules.index')
                ->with('success', 'Shift template created successfully');
        } catch (\Exception $e) {
            return redirect()
                ->route('department.schedules.index')
                ->with('error', 'Error creating template: ' . $e->getMessage());
        }
    }

    public function bulkAssign(Request $request)
    {
        $validated = $request->validate([
            'template_id' => 'required|exists:work_schedules,id',
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id',
            'week_start' => 'required|date'
        ]);

        $template = WorkSchedule::findOrFail($validated['template_id']);
        $weekStart = Carbon::parse($validated['week_start'])->startOfWeek();
        $weekEnd = $weekStart->copy()->endOfWeek();

        $workingDays = array_map('intval', $template->working_days);

        DB::beginTransaction();
        try {
            // First, remove any existing schedules for the selected week
            foreach ($validated['user_ids'] as $userId) {
                WorkSchedule::where('user_id', $userId)
                    ->where('department_id', auth()->user()->department_id)
                    ->where('week_start', $weekStart)
                    ->delete();
            }

            // Then create new schedules
            foreach ($validated['user_ids'] as $userId) {
                WorkSchedule::create([
                    'user_id' => $userId,
                    'department_id' => auth()->user()->department_id,
                    'shift_name' => $template->shift_name,
                    'start_time' => $template->start_time,
                    'end_time' => $template->end_time,
                    'working_days' => $workingDays,
                    'week_start' => $weekStart,
                    'week_end' => $weekEnd,
                    'is_template' => false
                ]);
            }

            DB::commit();
            return redirect()
                ->route('department.schedules.index')
                ->with('success', 'Shifts assigned successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()
                ->route('department.schedules.index')
                ->with('error', 'Error assigning shifts: ' . $e->getMessage());
        }
    }

    public function viewDepartmentSchedule(Request $request)
    {
        try {
            $department = auth()->user()->department;
            $start = Carbon::parse($request->get('start'));
            $end = Carbon::parse($request->get('end'));

            \Log::info('Department ID: ' . $department->id);
            \Log::info('Date Range: ' . $start . ' to ' . $end);

            $schedules = WorkSchedule::with('user')
                ->where('department_id', $department->id)
                ->where('is_template', false)
                ->get();

            \Log::info('Found schedules: ' . $schedules->count());

            $events = $this->formatSchedulesForCalendar($schedules);

            \Log::info('Formatted events: ' . count($events));

            return response()->json($events);
        } catch (\Exception $e) {
            \Log::error('Error in viewDepartmentSchedule: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $schedule = WorkSchedule::where('id', $id)
                ->where('department_id', auth()->user()->department_id)
                ->firstOrFail();

            $schedule->delete();

            return response()->json(['message' => 'Schedule removed successfully']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error removing schedule'], 500);
        }
    }

    private function formatSchedulesForCalendar($schedules)
    {
        $events = [];
        foreach ($schedules as $schedule) {
            $weekStart = Carbon::parse($schedule->week_start);

            $workingDays = array_map('intval', $schedule->working_days);

            foreach ($workingDays as $dayIndex) {
                $date = $weekStart->copy()->addDays($dayIndex);
                $startDateTime = Carbon::parse($date->format('Y-m-d') . ' ' . $schedule->start_time);
                $endDateTime = Carbon::parse($date->format('Y-m-d') . ' ' . $schedule->end_time);

                if ($startDateTime->greaterThan($endDateTime)) {
                    $endDateTime->addDay();
                }

                $events[] = [
                    'id' => $schedule->id,
                    'title' => $schedule->user->name . ' - ' . $schedule->shift_name,
                    'start' => $startDateTime->format('Y-m-d H:i:s'),
                    'end' => $endDateTime->format('Y-m-d H:i:s'),
                    'backgroundColor' => '#4CAF50',
                    'borderColor' => '#4CAF50',
                    'extendedProps' => [
                        'scheduleId' => $schedule->id,
                        'employeeName' => $schedule->user->name,
                        'shiftName' => $schedule->shift_name
                    ]
                ];
            }
        }
        return $events;
    }
}
