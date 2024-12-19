<x-layouts.app>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-6">
            <h1 class="text-2xl font-semibold">Department Work Schedules</h1>
            @if(session('success'))
                <div class="mt-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="mt-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                    {{ session('error') }}
                </div>
            @endif
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Shift Templates Section -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-lg font-semibold mb-4">Shift Templates</h2>

                    <!-- Create Template Form -->
                    <form action="{{ route('department.schedules.create-template') }}" method="POST" class="mb-6">
                        @csrf
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Template Name</label>
                                <input type="text" name="shift_name" required
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Start Time</label>
                                    <input type="time" name="start_time" required
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">End Time</label>
                                    <input type="time" name="end_time" required
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Working Days</label>
                                <div class="mt-2 grid grid-cols-7 gap-2">
                                    @foreach(['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'] as $index => $day)
                                        <label class="flex items-center">
                                            <input type="checkbox" name="working_days[]" value="{{ $index }}"
                                                   class="rounded border-gray-300 text-blue-600">
                                            <span class="ml-1 text-sm">{{ $day }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>

                            <button type="submit"
                                    class="w-full bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Create Template
                            </button>
                        </div>
                    </form>

                    <!-- Template List -->
                    <div class="space-y-4">
                        <h3 class="font-medium text-gray-900">Saved Templates</h3>
                        @foreach($templates as $template)
                            <div class="border rounded p-4">
                                <h3 class="font-medium">{{ $template->shift_name }}</h3>
                                <p class="text-sm text-gray-600">
                                    {{ Carbon\Carbon::parse($template->start_time)->format('H:i') }} -
                                    {{ Carbon\Carbon::parse($template->end_time)->format('H:i') }}
                                </p>
                                <div class="text-sm text-gray-500 mb-3">
                                    Working days:
                                    @foreach($template->working_days as $day)
                                        {{ ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'][$day] }}
                                    @endforeach
                                </div>
                                <div class="flex space-x-2">
                                    <button onclick="editTemplate({{ $template->id }})"
                                            class="text-blue-600 hover:text-blue-800 text-sm">
                                        Edit
                                    </button>
                                    <button onclick="deleteTemplate({{ $template->id }})"
                                            class="text-red-600 hover:text-red-800 text-sm">
                                        Delete
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Bulk Assignment Section -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-lg font-semibold mb-4">Assign Shifts</h2>

                    <form action="{{ route('department.schedules.bulk-assign') }}" method="POST" class="mb-6">
                        @csrf
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Select Template</label>
                                <select name="template_id" required
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                    @foreach($templates as $template)
                                        <option value="{{ $template->id }}">{{ $template->shift_name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Select Employees</label>
                                <div class="mt-2 space-y-2 max-h-60 overflow-y-auto">
                                    @foreach($employees as $employee)
                                        <label class="flex items-center">
                                            <input type="checkbox" name="user_ids[]" value="{{ $employee->id }}"
                                                   class="rounded border-gray-300 text-blue-600">
                                            <span class="ml-2">{{ $employee->name }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Week Starting</label>
                                <input type="date" name="week_start" required
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                            </div>

                            <button type="submit"
                                    class="w-full bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                                Assign Shifts
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Department Schedule Calendar -->
                <div class="bg-white rounded-lg shadow p-6 mt-6">
                    <h2 class="text-lg font-semibold mb-4">Department Schedule</h2>
                    <div id="calendar" class="min-h-[600px]"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Schedule Details Modal -->
    <div id="scheduleModal" style="display: none;"
         class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg font-medium" id="modalTitle"></h3>
                <div class="mt-2" id="modalContent"></div>
                <div class="mt-4 flex justify-end">
                    <button onclick="removeSchedule()"
                            class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600 mr-2">
                        Remove Schedule
                    </button>
                    <button onclick="closeModal()"
                            class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js'></script>
        <script>
            let calendar;

            // Template operations
            function editTemplate(templateId) {
                // We'll implement template editing later
                alert('Edit functionality coming soon');
            }

            function deleteTemplate(templateId) {
                if (confirm('Are you sure you want to delete this template? This cannot be undone.')) {
                    // Add template deletion logic here
                    alert('Delete functionality coming soon');
                }
            }

            // Schedule operations
            function showScheduleDetails(info) {
                if (confirm(`Are you sure you want to delete ${info.event.extendedProps.employeeName}'s shift?`)) {
                    deleteSchedule(info.event.extendedProps.scheduleId);
                }
            }

            function deleteSchedule(scheduleId) {
                fetch(`/department/schedules/${scheduleId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    }
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.message) {
                            calendar.refetchEvents();
                            alert('Schedule removed successfully');
                        } else {
                            throw new Error(data.error || 'Failed to remove schedule');
                        }
                    })
                    .catch(error => {
                        alert('Error removing schedule: ' + error.message);
                    });
            }

            document.addEventListener('DOMContentLoaded', function() {
                calendar = new FullCalendar.Calendar(document.getElementById('calendar'), {
                    initialView: 'timeGridWeek',
                    headerToolbar: {
                        left: 'prev,next today',
                        center: 'title',
                        right: 'timeGridWeek,timeGridDay'
                    },
                    slotMinTime: '00:00:00',
                    slotMaxTime: '24:00:00',
                    allDaySlot: false,
                    height: 'auto',
                    weekends: true,
                    events: '/department/schedules/view',
                    nowIndicator: true,
                    slotDuration: '01:00:00',
                    snapDuration: '00:15:00',
                    scrollTime: '00:00:00',
                    eventClick: showScheduleDetails,
                    slotLabelFormat: {
                        hour: '2-digit',
                        minute: '2-digit',
                        hour12: false
                    },
                    eventContent: function(arg) {
                        return {
                            html: `
                            <div class="p-1">
                                <div class="font-bold text-sm">${arg.event.extendedProps.employeeName}</div>
                                <div class="text-xs">${arg.event.extendedProps.shiftName}</div>
                            </div>
                        `
                        };
                    }
                });
                calendar.render();
            });
        </script>
    @endpush
</x-layouts.app>

