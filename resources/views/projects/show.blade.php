@extends('layouts.app')
@section('title', $project->name)

@section('breadcrumb')
<div class="flex items-center gap-2 text-sm">
    <a href="{{ route('projects.index') }}" class="text-gray-400 hover:text-gray-600 transition">Projects</a>
    <span class="text-gray-300 dark:text-gray-600">/</span>
    <span class="font-medium text-gray-700 dark:text-gray-200">{{ $project->name }}</span>
</div>
@endsection

@section('content')
<div class="p-6 space-y-6">
    <!-- Project Header -->
    <div class="bg-white dark:bg-[#13132a] border border-gray-100 dark:border-white/5 rounded-2xl p-6 shadow-sm">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="flex items-center gap-5">
                <div class="w-16 h-16 rounded-2xl flex items-center justify-center text-white font-bold text-2xl shadow-lg"
                     style="background: {{ $project->color ?? '#6366f1' }}">
                    {{ substr($project->name, 0, 1) }}
                </div>
                <div>
                    <div class="flex items-center gap-3 mb-1">
                        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $project->name }}</h1>
                        <span class="text-xs px-2.5 py-1 rounded-full font-medium
                            {{ $project->status === 'active' ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' :
                               ($project->status === 'completed' ? 'bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-400' :
                               'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400') }}">
                            {{ ucfirst(str_replace('_', ' ', $project->status)) }}
                        </span>
                    </div>
                    <p class="text-gray-500 dark:text-gray-400 text-sm max-w-2xl">{{ $project->description }}</p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('projects.kanban', $project) }}" class="flex items-center gap-2 px-4 py-2.5 rounded-xl border border-gray-200 dark:border-white/10 text-sm font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-white/5 transition">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2-2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2"/></svg>
                    Kanban Board
                </a>
                <a href="{{ route('projects.edit', $project) }}" class="p-2.5 rounded-xl border border-gray-200 dark:border-white/10 text-gray-500 hover:text-indigo-500 transition">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                </a>
            </div>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-6 mt-8 pt-6 border-t border-gray-50 dark:border-white/5">
            <div>
                <p class="text-xs text-gray-400 uppercase tracking-wider mb-1">Manager</p>
                <div class="flex items-center gap-2">
                    <img src="{{ $project->manager?->avatar_url }}" class="w-6 h-6 rounded-full">
                    <span class="text-sm font-medium text-gray-700 dark:text-gray-200">{{ $project->manager?->name ?? 'Unassigned' }}</span>
                </div>
            </div>
            <div>
                <p class="text-xs text-gray-400 uppercase tracking-wider mb-1">Due Date</p>
                <p class="text-sm font-medium {{ $project->due_date && $project->due_date->isPast() ? 'text-red-500' : 'text-gray-700 dark:text-gray-200' }}">
                    {{ $project->due_date ? $project->due_date->format('d M Y') : 'No deadline' }}
                </p>
            </div>
            <div>
                <p class="text-xs text-gray-400 uppercase tracking-wider mb-1">Priority</p>
                <span class="text-xs font-semibold {{ $project->priority === 'urgent' ? 'text-red-500' : ($project->priority === 'high' ? 'text-orange-500' : 'text-blue-500') }}">
                    {{ ucfirst($project->priority) }}
                </span>
            </div>
            <div>
                <p class="text-xs text-gray-400 uppercase tracking-wider mb-1">Budget</p>
                <p class="text-sm font-medium text-gray-700 dark:text-gray-200">
                    Rp {{ number_format($project->budget, 0, ',', '.') }}
                </p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content: Task List -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white dark:bg-[#13132a] border border-gray-100 dark:border-white/5 rounded-2xl overflow-hidden shadow-sm">
                <div class="px-6 py-4 border-b border-gray-50 dark:border-white/5 flex items-center justify-between">
                    <h3 class="font-bold text-gray-900 dark:text-white">Recent Tasks</h3>
                    <button onclick="document.getElementById('createTaskModal').classList.remove('hidden')" class="text-sm text-indigo-500 font-medium hover:underline">+ Add Task</button>
                </div>
                <div class="divide-y divide-gray-50 dark:divide-white/5">
                    @forelse($project->tasks->take(10) as $task)
                    <div class="px-6 py-4 flex items-center justify-between hover:bg-gray-50 dark:hover:bg-white/5 transition">
                        <div class="flex items-center gap-4">
                            <div class="w-2 h-2 rounded-full {{ $task->status === 'done' ? 'bg-green-500' : 'bg-gray-300 dark:bg-gray-700' }}"></div>
                            <div>
                                <a href="{{ route('tasks.show', $task) }}" class="text-sm font-medium text-gray-800 dark:text-gray-200 hover:text-indigo-500 transition line-clamp-1">
                                    {{ $task->title }}
                                </a>
                                <p class="text-xs text-gray-400">{{ ucfirst($task->status) }} · {{ $task->due_date ? $task->due_date->format('d M') : 'No date' }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            @if($task->assignee)
                            <img src="{{ $task->assignee->avatar_url }}" class="w-6 h-6 rounded-full" title="{{ $task->assignee->name }}">
                            @endif
                        </div>
                    </div>
                    @empty
                    <div class="p-10 text-center text-gray-400">
                        <p>Belum ada task di project ini.</p>
                    </div>
                    @endforelse
                </div>
            </div>

            <!-- Activity Log -->
            <div class="bg-white dark:bg-[#13132a] border border-gray-100 dark:border-white/5 rounded-2xl p-6 shadow-sm">
                <h3 class="font-bold text-gray-900 dark:text-white mb-6">Project Activity</h3>
                <div class="space-y-6">
                    @foreach($project->activities->take(8) as $activity)
                    <div class="flex gap-4 relative">
                        <!-- Timeline line -->
                        @if(!$loop->last)
                        <div class="absolute left-4 top-8 bottom-[-24px] w-px bg-gray-100 dark:bg-white/5"></div>
                        @endif
                        <img src="{{ $activity->user?->avatar_url }}" class="w-8 h-8 rounded-full z-10">
                        <div class="pb-2">
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                <span class="font-bold text-gray-900 dark:text-white">{{ $activity->user?->name }}</span>
                                {{ $activity->description }}
                            </p>
                            <p class="text-xs text-gray-400 mt-1">{{ $activity->created_at->diffForHumans() }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Sidebar: Stats & Members -->
        <div class="space-y-6">
            <!-- Progress Card -->
            <div class="bg-white dark:bg-[#13132a] border border-gray-100 dark:border-white/5 rounded-2xl p-6 shadow-sm">
                <h3 class="font-bold text-gray-900 dark:text-white mb-4">Overall Progress</h3>
                <div class="flex items-center justify-center py-6">
                    <div class="relative w-32 h-32">
                        <svg class="w-full h-full" viewBox="0 0 36 36">
                            <path class="text-gray-100 dark:text-white/5" stroke-dasharray="100, 100" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke="currentColor" stroke-width="3"></path>
                            <path class="text-indigo-500" stroke-dasharray="{{ $project->progress }}, 100" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round"></path>
                        </svg>
                        <div class="absolute inset-0 flex items-center justify-center flex-col">
                            <span class="text-2xl font-bold text-gray-900 dark:text-white">{{ $project->progress }}%</span>
                            <span class="text-[10px] text-gray-400 uppercase tracking-widest font-bold">Done</span>
                        </div>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4 mt-4">
                    <div class="text-center p-3 rounded-xl bg-gray-50 dark:bg-white/5">
                        <p class="text-lg font-bold text-gray-900 dark:text-white">{{ $stats['total'] }}</p>
                        <p class="text-[10px] text-gray-400 uppercase font-bold">Tasks</p>
                    </div>
                    <div class="text-center p-3 rounded-xl bg-gray-50 dark:bg-white/5">
                        <p class="text-lg font-bold text-green-500">{{ $stats['done'] }}</p>
                        <p class="text-[10px] text-gray-400 uppercase font-bold">Completed</p>
                    </div>
                </div>
            </div>

            <!-- Members -->
            <div class="bg-white dark:bg-[#13132a] border border-gray-100 dark:border-white/5 rounded-2xl p-6 shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-bold text-gray-900 dark:text-white">Team Members</h3>
                    <button class="p-1.5 rounded-lg text-gray-400 hover:bg-gray-50 dark:hover:bg-white/5 transition">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    </button>
                </div>
                <div class="space-y-4">
                    @foreach($project->members as $member)
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <img src="{{ $member->avatar_url }}" class="w-9 h-9 rounded-full object-cover">
                            <div>
                                <p class="text-sm font-semibold text-gray-800 dark:text-gray-200">{{ $member->name }}</p>
                                <p class="text-[10px] text-gray-400 uppercase font-bold tracking-widest">{{ $member->pivot->role }}</p>
                            </div>
                        </div>
                        @if($member->isOnline())
                        <div class="w-2 h-2 rounded-full bg-green-500 shadow-[0_0_8px_rgba(34,197,94,0.5)]"></div>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Client Portal Info (Jika ada client) -->
            @if($project->client)
            <div class="bg-indigo-500 rounded-2xl p-6 text-white shadow-lg shadow-indigo-500/20">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    </div>
                    <h3 class="font-bold">Client Portal</h3>
                </div>
                <p class="text-sm text-indigo-50 mb-4 opacity-90">Project ini terhubung dengan klien. Mereka dapat melihat progress secara realtime.</p>
                <div class="flex items-center gap-3 p-3 bg-white/10 rounded-xl">
                    <img src="{{ $project->client->avatar_url }}" class="w-8 h-8 rounded-full">
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-bold truncate">{{ $project->client->name }}</p>
                        <p class="text-[10px] opacity-70">Primary Contact</p>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal Create Task (Reusable snippet) -->
@include('components.task-modal')
@endsection
