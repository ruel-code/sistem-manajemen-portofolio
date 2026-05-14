@extends('layouts.app')
@section('title', 'Calendar')

@section('content')
<div class="p-6">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Calendar</h1>
        <div class="flex items-center gap-3 text-sm">
            <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-full bg-red-500"></span> Urgent</span>
            <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-full bg-orange-500"></span> High</span>
            <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-full bg-indigo-500"></span> Medium</span>
            <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-full bg-slate-400"></span> Low</span>
        </div>
    </div>
    <div class="bg-white dark:bg-[#13132a] border border-gray-100 dark:border-white/5 rounded-2xl p-6">
        <div id="calendar"></div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const calendarEl = document.getElementById('calendar');
    const calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,listWeek'
        },
        themeSystem: 'standard',
        height: 'auto',
        events: '/api/tasks/calendar',
        eventClick: function(info) {
            window.location.href = info.event.url;
            info.jsEvent.preventDefault();
        },
        eventDidMount: function(info) {
            info.el.title = info.event.title;
        }
    });
    calendar.render();
});
</script>
<style>
.fc { font-family: 'Inter', sans-serif; }
.dark .fc-theme-standard td, .dark .fc-theme-standard th { border-color: rgba(255,255,255,0.05); }
.dark .fc-col-header-cell-cushion, .dark .fc-daygrid-day-number { color: #94a3b8; }
.dark .fc-button { background: #1a1a2e !important; border-color: rgba(255,255,255,0.1) !important; color: #94a3b8 !important; }
.dark .fc-button-active { background: #6366f1 !important; color: white !important; }
.dark .fc-today-button { background: #6366f1 !important; border-color: #6366f1 !important; color: white !important; }
.dark .fc-daygrid-day.fc-day-today { background: rgba(99,102,241,0.1) !important; }
</style>
@endpush
