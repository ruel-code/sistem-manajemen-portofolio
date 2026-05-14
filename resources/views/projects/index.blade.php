@extends('layouts.app')
@section('title', 'Projects')

@section('breadcrumb')
<div class="flex items-center gap-2 text-sm">
    <a href="{{ route('dashboard') }}" class="text-gray-400 hover:text-gray-600 transition">Dashboard</a>
    <span class="text-gray-300 dark:text-gray-600">/</span>
    <span class="font-medium text-gray-700 dark:text-gray-200">Projects</span>
</div>
@endsection

@section('content')
<div class="p-6 space-y-6">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center gap-4">
        <div class="flex-1">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Projects</h1>
            <p class="text-sm text-gray-500 mt-0.5">Kelola semua project di workspace ini</p>
        </div>
        <div class="flex items-center gap-3">
            <!-- Filter -->
            <form method="GET" class="flex items-center gap-2">
                <select name="status" onchange="this.form.submit()"
                    class="text-sm rounded-xl border border-gray-200 dark:border-white/10 bg-white dark:bg-[#13132a] text-gray-700 dark:text-gray-300 px-3 py-2 outline-none focus:ring-2 focus:ring-indigo-500/20">
                    <option value="">All Status</option>
                    <option value="planning" {{ request('status') === 'planning' ? 'selected' : '' }}>Planning</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="review" {{ request('status') === 'review' ? 'selected' : '' }}>Review</option>
                    <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                </select>
            </form>
            <a href="{{ route('projects.create') }}"
               class="flex items-center gap-2 px-4 py-2 rounded-xl text-white text-sm font-medium shadow-lg transition-all hover:-translate-y-0.5"
               style="background: linear-gradient(135deg, #6366f1, #8b5cf6)">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                New Project
            </a>
        </div>
    </div>

    <!-- Projects Grid -->
    @if($projects->isNotEmpty())
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
        @foreach($projects as $project)
        <div class="bg-white dark:bg-[#13132a] border border-gray-100 dark:border-white/5 rounded-2xl overflow-hidden hover:shadow-xl hover:shadow-indigo-500/5 dark:hover:shadow-indigo-500/10 transition-all duration-300 group">
            <!-- Color Banner -->
            <div class="h-2" style="background: linear-gradient(90deg, {{ $project->color ?? '#6366f1' }}, {{ $project->color ?? '#8b5cf6' }}88)"></div>

            <div class="p-5">
                <!-- Header -->
                <div class="flex items-start justify-between mb-3">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center text-white font-bold text-sm flex-shrink-0"
                             style="background: {{ $project->color ?? '#6366f1' }}">
                            {{ substr($project->name, 0, 2) }}
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-900 dark:text-white group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition text-sm leading-tight">
                                {{ $project->name }}
                            </h3>
                            <div class="flex items-center gap-1.5 mt-0.5">
                                <span class="text-xs px-2 py-0.5 rounded-full font-medium
                                    {{ $project->status === 'active' ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' :
                                       ($project->status === 'completed' ? 'bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-400' :
                                       ($project->status === 'review' ? 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400' :
                                       'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400')) }}">
                                    {{ ucfirst(str_replace('_', ' ', $project->status)) }}
                                </span>
                                <span class="text-xs px-2 py-0.5 rounded-full
                                    {{ $project->priority === 'urgent' ? 'bg-red-100 text-red-600 dark:bg-red-900/30 dark:text-red-400' :
                                       ($project->priority === 'high' ? 'bg-orange-100 text-orange-600 dark:bg-orange-900/30 dark:text-orange-400' :
                                       'bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-400') }}">
                                    {{ ucfirst($project->priority) }}
                                </span>
                            </div>
                        </div>
                    </div>
                    <!-- 3-dot menu -->
                    <div x-data="{ open: false }" class="relative">
                        <button @click="open = !open" class="p-1.5 rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100 dark:hover:bg-white/10 transition">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="5" r="1.5"/><circle cx="12" cy="12" r="1.5"/><circle cx="12" cy="19" r="1.5"/></svg>
                        </button>
                        <div x-show="open" @click.outside="open = false"
                             class="absolute right-0 top-8 w-40 bg-white dark:bg-[#1a1a2e] border border-gray-100 dark:border-white/10 rounded-xl shadow-xl z-10 py-1">
                            <a href="{{ route('projects.show', $project) }}" class="flex items-center gap-2 px-3 py-2 text-sm text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-white/5">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                View
                            </a>
                            <a href="{{ route('projects.kanban', $project) }}" class="flex items-center gap-2 px-3 py-2 text-sm text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-white/5">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2"/></svg>
                                Kanban
                            </a>
                            <a href="{{ route('projects.edit', $project) }}" class="flex items-center gap-2 px-3 py-2 text-sm text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-white/5">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                Edit
                            </a>
                            <hr class="my-1 border-gray-100 dark:border-white/5">
                            <form action="{{ route('projects.destroy', $project) }}" method="POST" onsubmit="return confirm('Hapus project ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="w-full flex items-center gap-2 px-3 py-2 text-sm text-red-500 hover:bg-red-50 dark:hover:bg-red-900/10">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    Delete
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Description -->
                @if($project->description)
                <p class="text-xs text-gray-500 dark:text-gray-400 mb-4 line-clamp-2">{{ $project->description }}</p>
                @endif

                <!-- Progress -->
                <div class="mb-4">
                    <div class="flex items-center justify-between mb-1.5">
                        <span class="text-xs text-gray-500">Progress</span>
                        <span class="text-xs font-semibold text-gray-700 dark:text-gray-300">{{ $project->progress }}%</span>
                    </div>
                    <div class="w-full bg-gray-100 dark:bg-white/10 rounded-full h-2">
                        <div class="h-2 rounded-full progress-bar" style="width: {{ $project->progress }}%; background: {{ $project->color ?? '#6366f1' }}"></div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="flex items-center justify-between">
                    <!-- Member avatars -->
                    <div class="flex -space-x-2">
                        @foreach($project->members->take(4) as $member)
                        <img src="{{ $member->avatar_url }}" alt="{{ $member->name }}"
                             class="w-7 h-7 rounded-full border-2 border-white dark:border-[#13132a]"
                             title="{{ $member->name }}">
                        @endforeach
                        @if($project->members->count() > 4)
                        <div class="w-7 h-7 rounded-full border-2 border-white dark:border-[#13132a] bg-gray-200 dark:bg-gray-700 flex items-center justify-center text-xs font-medium text-gray-600 dark:text-gray-300">
                            +{{ $project->members->count() - 4 }}
                        </div>
                        @endif
                    </div>
                    <!-- Due date -->
                    @if($project->due_date)
                    <div class="flex items-center gap-1 text-xs {{ $project->due_date->isPast() && $project->status !== 'completed' ? 'text-red-500' : 'text-gray-400' }}">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        {{ $project->due_date->format('d M Y') }}
                    </div>
                    @endif
                </div>

                <!-- Task counts -->
                <div class="flex items-center gap-3 mt-3 pt-3 border-t border-gray-50 dark:border-white/5">
                    <span class="text-xs text-gray-400">
                        <span class="font-medium text-gray-700 dark:text-gray-300">{{ $project->tasks->count() }}</span> tasks
                    </span>
                    <span class="text-xs text-gray-400">
                        <span class="font-medium text-green-600 dark:text-green-400">{{ $project->tasks->where('status', 'done')->count() }}</span> done
                    </span>
                </div>
            </div>

            <!-- Quick action -->
            <div class="px-5 pb-4">
                <a href="{{ route('projects.show', $project) }}"
                   class="w-full flex items-center justify-center gap-2 py-2 rounded-xl text-sm font-medium text-indigo-600 dark:text-indigo-400 border border-indigo-200 dark:border-indigo-500/30 hover:bg-indigo-50 dark:hover:bg-indigo-500/10 transition">
                    Open Project →
                </a>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Pagination -->
    <div class="mt-6">{{ $projects->links() }}</div>

    @else
    <!-- Empty State -->
    <div class="text-center py-20">
        <div class="w-16 h-16 rounded-2xl flex items-center justify-center mx-auto mb-4" style="background: linear-gradient(135deg, #6366f130, #8b5cf630)">
            <svg class="w-8 h-8 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
        </div>
        <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-2">Belum ada project</h3>
        <p class="text-gray-400 mb-6">Mulai dengan membuat project pertama Anda</p>
        <a href="{{ route('projects.create') }}"
           class="inline-flex items-center gap-2 px-6 py-3 rounded-xl text-white font-medium shadow-lg hover:-translate-y-0.5 transition-all"
           style="background: linear-gradient(135deg, #6366f1, #8b5cf6)">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Buat Project Pertama
        </a>
    </div>
    @endif
</div>
@endsection
