@extends('layouts.app')

@section('title', 'Dashboard')

@section('breadcrumb')
<div class="flex items-center gap-2 text-sm">
    <span class="text-gray-400 dark:text-gray-500">NexaCRM</span>
    <span class="text-gray-300 dark:text-gray-600">/</span>
    <span class="font-medium text-gray-700 dark:text-gray-200">Dashboard</span>
</div>
@endsection

@section('content')
<div class="p-6 space-y-6">

    <!-- Welcome Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                Selamat datang, {{ auth()->user()->name }}! 👋
            </h1>
            <p class="text-gray-500 dark:text-gray-400 text-sm mt-1">
                {{ now()->translatedFormat('l, d F Y') }} &bull;
                <span class="text-indigo-500">{{ is_array($workspace) ? $workspace['name'] : $workspace->name }}</span>
            </p>
        </div>
        <a href="{{ route('projects.create') }}"
           class="flex items-center gap-2 px-4 py-2.5 rounded-xl text-white text-sm font-medium shadow-lg transition-all hover:-translate-y-0.5 hover:shadow-indigo-200/50 dark:hover:shadow-indigo-500/20"
           style="background: linear-gradient(135deg, #6366f1, #8b5cf6)">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            New Project
        </a>
    </div>

    <!-- ===================== STAT CARDS ===================== -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">

        <!-- Total Projects -->
        <div class="stat-card bg-white dark:bg-[#13132a] border border-gray-100 dark:border-white/5 rounded-2xl p-5">
            <div class="flex items-center justify-between mb-4">
                <div class="w-11 h-11 rounded-xl flex items-center justify-center" style="background: linear-gradient(135deg, #6366f1, #818cf8)">
                    <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                </div>
                <span class="text-xs font-medium px-2 py-1 rounded-lg bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400">
                    {{ $stats['active_projects'] }} aktif
                </span>
            </div>
            <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ $stats['total_projects'] }}</p>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Total Projects</p>
        </div>

        <!-- Total Tasks -->
        <div class="stat-card bg-white dark:bg-[#13132a] border border-gray-100 dark:border-white/5 rounded-2xl p-5">
            <div class="flex items-center justify-between mb-4">
                <div class="w-11 h-11 rounded-xl flex items-center justify-center" style="background: linear-gradient(135deg, #8b5cf6, #a78bfa)">
                    <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                </div>
                <span class="text-xs font-medium px-2 py-1 rounded-lg bg-purple-50 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400">
                    {{ $stats['completed_tasks'] }} selesai
                </span>
            </div>
            <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ $stats['total_tasks'] }}</p>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Total Tasks</p>
        </div>

        <!-- Team Members -->
        <div class="stat-card bg-white dark:bg-[#13132a] border border-gray-100 dark:border-white/5 rounded-2xl p-5">
            <div class="flex items-center justify-between mb-4">
                <div class="w-11 h-11 rounded-xl flex items-center justify-center" style="background: linear-gradient(135deg, #06b6d4, #22d3ee)">
                    <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                </div>
                <span class="text-xs font-medium px-2 py-1 rounded-lg bg-cyan-50 dark:bg-cyan-900/30 text-cyan-600 dark:text-cyan-400">
                    online
                </span>
            </div>
            <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ $stats['total_members'] }}</p>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Team Members</p>
        </div>

        <!-- Revenue -->
        <div class="stat-card bg-white dark:bg-[#13132a] border border-gray-100 dark:border-white/5 rounded-2xl p-5">
            <div class="flex items-center justify-between mb-4">
                <div class="w-11 h-11 rounded-xl flex items-center justify-center" style="background: linear-gradient(135deg, #10b981, #34d399)">
                    <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <span class="text-xs font-medium px-2 py-1 rounded-lg bg-green-50 dark:bg-green-900/30 text-green-600 dark:text-green-400">
                    paid
                </span>
            </div>
            <p class="text-xl font-bold text-gray-900 dark:text-white">Rp {{ number_format($stats['revenue_total'], 0, ',', '.') }}</p>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Total Revenue</p>
        </div>
    </div>

    <!-- ===================== CHARTS ROW ===================== -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Weekly Progress Chart -->
        <div class="lg:col-span-2 bg-white dark:bg-[#13132a] border border-gray-100 dark:border-white/5 rounded-2xl p-6">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="font-semibold text-gray-900 dark:text-white">Task Progress</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">7 hari terakhir</p>
                </div>
                <div class="flex items-center gap-4 text-xs text-gray-500">
                    <span class="flex items-center gap-1">
                        <span class="w-3 h-3 rounded-full bg-indigo-500 inline-block"></span> Completed
                    </span>
                </div>
            </div>
            <canvas id="weeklyChart" height="80"></canvas>
        </div>

        <!-- Task by Status Donut -->
        <div class="bg-white dark:bg-[#13132a] border border-gray-100 dark:border-white/5 rounded-2xl p-6">
            <h3 class="font-semibold text-gray-900 dark:text-white mb-1">Tasks by Status</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">Distribusi tasks</p>
            <canvas id="donutChart" height="160"></canvas>
            <div class="mt-4 space-y-2">
                @foreach([['todo','To Do','#64748b'],['in_progress','In Progress','#6366f1'],['review','Review','#f59e0b'],['done','Done','#10b981']] as [$status,$label,$color])
                <div class="flex items-center justify-between text-sm">
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full flex-shrink-0" style="background:{{ $color }}"></span>
                        <span class="text-gray-600 dark:text-gray-400">{{ $label }}</span>
                    </div>
                    <span class="font-medium text-gray-900 dark:text-white">{{ $tasksByStatus[$status] ?? 0 }}</span>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- ===================== PROJECTS & TASKS ===================== -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Recent Projects -->
        <div class="lg:col-span-2 bg-white dark:bg-[#13132a] border border-gray-100 dark:border-white/5 rounded-2xl p-6">
            <div class="flex items-center justify-between mb-5">
                <h3 class="font-semibold text-gray-900 dark:text-white">Recent Projects</h3>
                <a href="{{ route('projects.index') }}" class="text-sm text-indigo-500 hover:text-indigo-600 transition">View all →</a>
            </div>
            <div class="space-y-4">
                @forelse($recentProjects as $project)
                <a href="{{ route('projects.show', $project) }}" class="flex items-center gap-4 p-3 rounded-xl hover:bg-gray-50 dark:hover:bg-white/5 transition group">
                    <!-- Color dot -->
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0 text-white text-sm font-bold"
                         style="background: {{ $project->color ?? '#6366f1' }}">
                        {{ substr($project->name, 0, 1) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 mb-1">
                            <p class="text-sm font-semibold text-gray-800 dark:text-gray-200 truncate group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition">
                                {{ $project->name }}
                            </p>
                            <span class="text-xs px-2 py-0.5 rounded-full font-medium flex-shrink-0
                                {{ $project->status === 'active' ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' :
                                   ($project->status === 'completed' ? 'bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-400' :
                                   'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400') }}">
                                {{ ucfirst(str_replace('_', ' ', $project->status)) }}
                            </span>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="flex-1 bg-gray-100 dark:bg-white/10 rounded-full h-1.5">
                                <div class="h-1.5 rounded-full progress-bar" style="width: {{ $project->progress }}%; background: {{ $project->color ?? '#6366f1' }}"></div>
                            </div>
                            <span class="text-xs text-gray-500 flex-shrink-0">{{ $project->progress }}%</span>
                        </div>
                    </div>
                    @if($project->due_date)
                    <div class="text-right flex-shrink-0">
                        <p class="text-xs text-gray-400">Due</p>
                        <p class="text-xs font-medium {{ $project->due_date->isPast() ? 'text-red-500' : 'text-gray-600 dark:text-gray-400' }}">
                            {{ $project->due_date->format('d M') }}
                        </p>
                    </div>
                    @endif
                </a>
                @empty
                <div class="text-center py-8 text-gray-400">
                    <p>Belum ada project. <a href="{{ route('projects.create') }}" class="text-indigo-500">Buat sekarang →</a></p>
                </div>
                @endforelse
            </div>
        </div>

        <!-- My Tasks -->
        <div class="bg-white dark:bg-[#13132a] border border-gray-100 dark:border-white/5 rounded-2xl p-6">
            <div class="flex items-center justify-between mb-5">
                <h3 class="font-semibold text-gray-900 dark:text-white">My Tasks</h3>
                <span class="text-xs bg-indigo-100 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 px-2 py-1 rounded-lg font-medium">
                    {{ $myTasks->count() }} pending
                </span>
            </div>
            <div class="space-y-3">
                @forelse($myTasks as $task)
                <div class="flex items-start gap-3 p-2.5 rounded-lg hover:bg-gray-50 dark:hover:bg-white/5 transition">
                    <div class="w-2 h-2 rounded-full mt-1.5 flex-shrink-0
                        {{ $task->priority === 'urgent' ? 'bg-red-500' :
                           ($task->priority === 'high' ? 'bg-orange-500' :
                           ($task->priority === 'medium' ? 'bg-indigo-500' : 'bg-slate-400')) }}">
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm text-gray-800 dark:text-gray-200 truncate">{{ $task->title }}</p>
                        <p class="text-xs text-gray-400 mt-0.5">{{ $task->project->name }}</p>
                    </div>
                    @if($task->due_date)
                    <span class="text-xs flex-shrink-0 {{ $task->is_overdue ? 'text-red-500 font-medium' : 'text-gray-400' }}">
                        {{ $task->due_date->format('d/m') }}
                    </span>
                    @endif
                </div>
                @empty
                <div class="text-center py-6 text-gray-400 text-sm">
                    <p>🎉 Semua task selesai!</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- ===================== ACTIVITY FEED ===================== -->
    <div class="bg-white dark:bg-[#13132a] border border-gray-100 dark:border-white/5 rounded-2xl p-6">
        <h3 class="font-semibold text-gray-900 dark:text-white mb-5">Recent Activity</h3>
        <div class="space-y-4">
            @forelse($recentActivities as $activity)
            <div class="flex items-start gap-3">
                <img src="{{ $activity->user?->avatar_url }}" alt="" class="w-8 h-8 rounded-full flex-shrink-0">
                <div class="flex-1">
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        <span class="font-medium text-gray-900 dark:text-white">{{ $activity->user?->name }}</span>
                        {{ $activity->description }}
                    </p>
                    <p class="text-xs text-gray-400 mt-0.5">{{ $activity->created_at->diffForHumans() }}</p>
                </div>
            </div>
            @empty
            <p class="text-sm text-gray-400 text-center py-4">Belum ada aktivitas</p>
            @endforelse
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const isDark = document.documentElement.classList.contains('dark');
    const textColor = isDark ? '#94a3b8' : '#64748b';
    const gridColor = isDark ? 'rgba(255,255,255,0.05)' : 'rgba(0,0,0,0.05)';

    // Weekly Chart
    const weeklyData = @json($weeklyProgress);
    new Chart(document.getElementById('weeklyChart'), {
        type: 'bar',
        data: {
            labels: weeklyData.map(d => d.date),
            datasets: [{
                label: 'Tasks Completed',
                data: weeklyData.map(d => d.count),
                backgroundColor: 'rgba(99,102,241,0.7)',
                borderColor: '#6366f1',
                borderWidth: 2,
                borderRadius: 8,
                borderSkipped: false,
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: gridColor },
                    ticks: { color: textColor, stepSize: 1 }
                },
                x: {
                    grid: { display: false },
                    ticks: { color: textColor }
                }
            }
        }
    });

    // Donut Chart
    const statusData = @json($tasksByStatus);
    new Chart(document.getElementById('donutChart'), {
        type: 'doughnut',
        data: {
            labels: ['To Do', 'In Progress', 'Review', 'Done'],
            datasets: [{
                data: [
                    statusData.todo || 0,
                    statusData.in_progress || 0,
                    statusData.review || 0,
                    statusData.done || 0,
                ],
                backgroundColor: ['#64748b', '#6366f1', '#f59e0b', '#10b981'],
                borderWidth: 0,
                hoverOffset: 8,
            }]
        },
        options: {
            responsive: true,
            cutout: '70%',
            plugins: { legend: { display: false } }
        }
    });
});
</script>
@endpush
