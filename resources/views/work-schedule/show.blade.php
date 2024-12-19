<x-layouts.app>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-6">
            <h1 class="text-2xl font-semibold">My Work Schedule</h1>
            @if($schedule)
                <div class="mt-4 bg-white rounded-lg shadow p-4">
                    <div class="mb-4">
                        <h2 class="text-lg font-medium">Current Shift Details</h2>
                        <div class="mt-2 grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm text-gray-600">Shift Name</p>
                                <p class="font-medium">{{ $schedule->shift_name }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Working Hours</p>
                                <p class="font-medium">
                                    {{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }} -
                                    {{ \Carbon\Carbon::parse($schedule->end_time)->format('H:i') }}
                                    @if(\Carbon\Carbon::parse($schedule->start_time)->greaterThan(\Carbon\Carbon::parse($schedule->end_time)))
                                        (Next Day)
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Calendar -->
        <div class="bg-white rounded-lg shadow">
            <div class="p-4">
                <div id="calendar" class="min-h-[800px]"></div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js'></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const calendarEl = document.getElementById('calendar');
                const calendar = new FullCalendar.Calendar(calendarEl, {
                    initialView: 'timeGridWeek',
                    headerToolbar: {
                        left: 'prev,next today',
                        center: 'title',
                        right: 'timeGridWeek,timeGridDay'
                    },
                    // Show full 24 hours
                    slotMinTime: '00:00:00',
                    slotMaxTime: '24:00:00',
                    allDaySlot: true,
                    height: 'auto',
                    weekends: true,
                    events: '/work-schedule/events',
                    nowIndicator: true, // Shows current time indicator
                    slotDuration: '01:00:00', // 1-hour slots
                    snapDuration: '00:15:00', // 15-minute snap intervals
                    scrollTime: '00:00:00', // Start scrolled to midnight
                    eventContent: function(arg) {
                        if (arg.event.allDay) {
                            return {
                                html: '<div class="p-1 text-center">' + arg.event.title + '</div>'
                            };
                        }

                        // Format the time display
                        const start = new Date(arg.event.start);
                        const end = new Date(arg.event.end);
                        const timeText = start.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }) +
                            ' - ' +
                            end.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });

                        return {
                            html: '<div class="p-1">' +
                                '<div class="font-bold">' + arg.event.title + '</div>' +
                                '<div class="text-sm">' + timeText + '</div>' +
                                '</div>'
                        };
                    },
                    eventDidMount: function(info) {
                        if (info.event.extendedProps.shiftType === 'off') {
                            info.el.style.opacity = '0.7';
                        }
                    },
                    slotLabelFormat: {
                        hour: '2-digit',
                        minute: '2-digit',
                        hour12: false // Use 24-hour format
                    }
                });
                calendar.render();
            });
        </script>
    @endpush
</x-layouts.app>
