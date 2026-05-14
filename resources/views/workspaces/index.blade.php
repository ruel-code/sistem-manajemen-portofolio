@extends('layouts.app')
@section('title', 'Workspaces')

@section('content')
<div class="p-6 space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Workspaces</h1>
        <a href="{{ route('workspaces.create') }}"
           class="flex items-center gap-2 px-4 py-2.5 rounded-xl text-white text-sm font-medium shadow-lg hover:-translate-y-0.5 transition-all"
           style="background: linear-gradient(135deg, #6366f1, #8b5cf6)">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            New Workspace
        </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
        @forelse($workspaces as $workspace)
        <div class="bg-white dark:bg-[#13132a] border border-gray-100 dark:border-white/5 rounded-2xl overflow-hidden hover:shadow-xl transition-all group">
            <div class="h-2" style="background: {{ $workspace->color ?? '#6366f1' }}"></div>
            <div class="p-6">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-12 h-12 rounded-xl flex items-center justify-center text-white font-bold"
                         style="background: {{ $workspace->color ?? '#6366f1' }}">
                        {{ substr($workspace->name, 0, 2) }}
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-900 dark:text-white">{{ $workspace->name }}</h3>
                        <p class="text-xs text-gray-400">{{ ucfirst($workspace->plan) }} Plan</p>
                    </div>
                </div>
                <p class="text-sm text-gray-400 mb-4 line-clamp-2">{{ $workspace->description ?? 'No description' }}</p>
                <div class="flex items-center justify-between text-sm text-gray-500">
                    <span>{{ $workspace->projects->count() }} projects</span>
                    <div class="flex items-center gap-2">
                        <form action="{{ route('workspaces.switch', $workspace) }}" method="POST">
                            @csrf
                            <button type="submit" class="px-3 py-1.5 rounded-lg text-xs font-medium text-indigo-600 dark:text-indigo-400 border border-indigo-200 dark:border-indigo-500/30 hover:bg-indigo-50 dark:hover:bg-indigo-500/10 transition">
                                Switch
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-full text-center py-16">
            <p class="text-gray-400 mb-4">Belum ada workspace</p>
            <a href="{{ route('workspaces.create') }}" class="text-indigo-500 hover:text-indigo-700 font-medium">Buat workspace pertama →</a>
        </div>
        @endforelse
    </div>
</div>
@endsection
