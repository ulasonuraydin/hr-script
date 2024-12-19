<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js'></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (document.getElementById('calendar')) {
            const calendar = new FullCalendar.Calendar(document.getElementById('calendar'), {
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
                    const timeText = start.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', hour12: false }) +
                        ' - ' +
                        end.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', hour12: false });

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
        }
    });
</script>
