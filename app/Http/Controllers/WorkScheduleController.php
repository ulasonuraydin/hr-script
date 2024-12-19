<?php

namespace App\Http\Controllers;

use App\Models\WorkSchedule;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class WorkScheduleController extends Controller
{
    public function mySchedule()
    {
        // Get the current week's schedule
        $currentWeekStart = Carbon::now()->startOfWeek();
        $currentWeekEnd = Carbon::now()->endOfWeek();

        $schedule = WorkSchedule::where('user_id', Auth::id())
            ->where('week_start', '<=', $currentWeekEnd)
            ->where('week_end', '>=', $currentWeekStart)
            ->first();

        return view('work-schedule.show', compact('schedule'));
    }

    public function events(Request $request)
    {
        $start = Carbon::parse($request->start);
        $end = Carbon::parse($request->end);

        $schedules = WorkSchedule::where('user_id', Auth::id())
            ->where(function($query) use ($start, $end) {
                $query->whereBetween('week_start', [$start, $end])
                    ->orWhereBetween('week_end', [$start, $end]);
            })
            ->get();

        $events = [];

        foreach ($schedules as $schedule) {
            $weekStart = Carbon::parse($schedule->week_start);
            $weekDays = range(0, 6); // 0 (Sunday) to 6 (Saturday)

            foreach ($weekDays as $dayIndex) {
                $date = $weekStart->copy()->addDays($dayIndex);

                // Skip if the date is outside the requested range
                if (!$date->between($start, $end)) {
                    continue;
                }

                // Check if this is a working day
                if (in_array($dayIndex, $schedule->working_days)) {
                    $startDateTime = Carbon::parse($date->format('Y-m-d') . ' ' . $schedule->start_time);
                    $endDateTime = Carbon::parse($date->format('Y-m-d') . ' ' . $schedule->end_time);

                    // Handle overnight shifts
                    if ($startDateTime->greaterThan($endDateTime)) {
                        $endDateTime->addDay();
                    }

                    $events[] = [
                        'title' => $schedule->shift_name,
                        'start' => $startDateTime->format('Y-m-d H:i:s'),
                        'end' => $endDateTime->format('Y-m-d H:i:s'),
                        'backgroundColor' => '#4CAF50',
                        'borderColor' => '#4CAF50',
                        'extendedProps' => [
                            'shiftType' => 'work'
                        ]
                    ];
                } else {
                    // Mark non-working days
                    $events[] = [
                        'title' => 'Off Day',
                        'start' => $date->format('Y-m-d'),
                        'end' => $date->format('Y-m-d'),
                        'backgroundColor' => '#F5F5F5',
                        'borderColor' => '#E0E0E0',
                        'textColor' => '#9E9E9E',
                        'allDay' => true,
                        'extendedProps' => [
                            'shiftType' => 'off'
                        ]
                    ];
                }
            }
        }

        return response()->json($events);
    }
}
