@extends('layouts.app')
@section('title', 'Create Workspace')

@section('content')
<div class="min-h-screen flex items-center justify-center p-6">
    <div class="w-full max-w-lg">
        <!-- Logo -->
        <div class="text-center mb-8">
            <div class="w-14 h-14 rounded-2xl flex items-center justify-center mx-auto mb-4"
                 style="background: linear-gradient(135deg, #6366f1, #8b5cf6)">
                <svg class="w-7 h-7 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
            </div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Create Your Workspace</h1>
            <p class="text-gray-400 text-sm mt-1">Setup workspace untuk tim Anda</p>
        </div>

        <div class="bg-white dark:bg-[#13132a] border border-gray-100 dark:border-white/5 rounded-2xl p-8 shadow-xl">
            <form method="POST" action="{{ route('workspaces.store') }}" class="space-y-5">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Workspace Name *</label>
                    <input type="text" name="name" required autofocus value="{{ old('name') }}"
                        placeholder="Contoh: Acme Digital Agency"
                        class="w-full rounded-xl border border-gray-200 dark:border-white/10 bg-gray-50 dark:bg-[#0f0f1a] text-gray-900 dark:text-gray-100 px-4 py-3 text-sm outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-400">
                    @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Description</label>
                    <textarea name="description" rows="2" placeholder="Deskripsi workspace..."
                        class="w-full rounded-xl border border-gray-200 dark:border-white/10 bg-gray-50 dark:bg-[#0f0f1a] text-gray-900 dark:text-gray-100 px-4 py-3 text-sm outline-none focus:ring-2 focus:ring-indigo-500/20 resize-none">{{ old('description') }}</textarea>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Workspace Color</label>
                    <div class="flex items-center gap-3">
                        @foreach(['#6366f1','#8b5cf6','#06b6d4','#10b981','#f59e0b','#ef4444','#ec4899','#f97316'] as $color)
                        <label>
                            <input type="radio" name="color" value="{{ $color }}" class="sr-only peer" {{ old('color','#6366f1') === $color ? 'checked' : '' }}>
                            <span class="w-8 h-8 rounded-xl cursor-pointer block ring-2 ring-transparent peer-checked:ring-offset-2 dark:peer-checked:ring-offset-[#13132a] peer-checked:ring-indigo-400 transition"
                                  style="background: {{ $color }}"></span>
                        </label>
                        @endforeach
                    </div>
                </div>

                <button type="submit"
                    class="w-full py-3 rounded-xl text-white font-medium shadow-lg transition-all hover:-translate-y-0.5"
                    style="background: linear-gradient(135deg, #6366f1, #8b5cf6)">
                    Create Workspace 🚀
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
