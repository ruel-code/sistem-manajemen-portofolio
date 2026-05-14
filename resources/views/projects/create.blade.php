@extends('layouts.app')
@section('title', 'Create Project')

@section('breadcrumb')
<div class="flex items-center gap-2 text-sm">
    <a href="{{ route('projects.index') }}" class="text-gray-400 hover:text-gray-600">Projects</a>
    <span class="text-gray-300 dark:text-gray-600">/</span>
    <span class="font-medium text-gray-700 dark:text-gray-200">Create</span>
</div>
@endsection

@section('content')
<div class="p-6 max-w-3xl mx-auto">
    <div class="bg-white dark:bg-[#13132a] border border-gray-100 dark:border-white/5 rounded-2xl p-8">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-1">Create New Project</h1>
        <p class="text-gray-400 text-sm mb-8">Isi detail project baru Anda</p>

        <form method="POST" action="{{ route('projects.store') }}" enctype="multipart/form-data" class="space-y-5">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <!-- Name -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Project Name *</label>
                    <input type="text" name="name" value="{{ old('name') }}" required
                        placeholder="Contoh: Website Company Profile"
                        class="w-full rounded-xl border border-gray-200 dark:border-white/10 bg-gray-50 dark:bg-[#0f0f1a] text-gray-900 dark:text-gray-100 px-4 py-3 text-sm outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-400 @error('name') border-red-400 @enderror">
                    @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <!-- Description -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Description</label>
                    <textarea name="description" rows="3" placeholder="Deskripsi project..."
                        class="w-full rounded-xl border border-gray-200 dark:border-white/10 bg-gray-50 dark:bg-[#0f0f1a] text-gray-900 dark:text-gray-100 px-4 py-3 text-sm outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-400 resize-none">{{ old('description') }}</textarea>
                </div>

                <!-- Status -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Status *</label>
                    <select name="status" class="w-full rounded-xl border border-gray-200 dark:border-white/10 bg-gray-50 dark:bg-[#0f0f1a] text-gray-900 dark:text-gray-100 px-4 py-3 text-sm outline-none focus:ring-2 focus:ring-indigo-500/20">
                        <option value="planning" {{ old('status') === 'planning' ? 'selected' : '' }}>Planning</option>
                        <option value="active" {{ old('status') === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="review" {{ old('status') === 'review' ? 'selected' : '' }}>Review</option>
                        <option value="completed" {{ old('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="on_hold" {{ old('status') === 'on_hold' ? 'selected' : '' }}>On Hold</option>
                    </select>
                </div>

                <!-- Priority -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Priority *</label>
                    <select name="priority" class="w-full rounded-xl border border-gray-200 dark:border-white/10 bg-gray-50 dark:bg-[#0f0f1a] text-gray-900 dark:text-gray-100 px-4 py-3 text-sm outline-none focus:ring-2 focus:ring-indigo-500/20">
                        <option value="low">🔵 Low</option>
                        <option value="medium" selected>🟡 Medium</option>
                        <option value="high">🟠 High</option>
                        <option value="urgent">🔴 Urgent</option>
                    </select>
                </div>

                <!-- Start Date -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Start Date</label>
                    <input type="date" name="start_date" value="{{ old('start_date') }}"
                        class="w-full rounded-xl border border-gray-200 dark:border-white/10 bg-gray-50 dark:bg-[#0f0f1a] text-gray-900 dark:text-gray-100 px-4 py-3 text-sm outline-none focus:ring-2 focus:ring-indigo-500/20">
                </div>

                <!-- Due Date -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Due Date</label>
                    <input type="date" name="due_date" value="{{ old('due_date') }}"
                        class="w-full rounded-xl border border-gray-200 dark:border-white/10 bg-gray-50 dark:bg-[#0f0f1a] text-gray-900 dark:text-gray-100 px-4 py-3 text-sm outline-none focus:ring-2 focus:ring-indigo-500/20">
                </div>

                <!-- Manager -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Project Manager</label>
                    <select name="manager_id" class="w-full rounded-xl border border-gray-200 dark:border-white/10 bg-gray-50 dark:bg-[#0f0f1a] text-gray-900 dark:text-gray-100 px-4 py-3 text-sm outline-none focus:ring-2 focus:ring-indigo-500/20">
                        <option value="">-- Pilih Manager --</option>
                        @foreach($members as $member)
                        <option value="{{ $member->id }}" {{ old('manager_id') == $member->id ? 'selected' : '' }}>
                            {{ $member->name }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <!-- Budget -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Budget (Rp)</label>
                    <input type="number" name="budget" value="{{ old('budget') }}" min="0" step="1000" placeholder="0"
                        class="w-full rounded-xl border border-gray-200 dark:border-white/10 bg-gray-50 dark:bg-[#0f0f1a] text-gray-900 dark:text-gray-100 px-4 py-3 text-sm outline-none focus:ring-2 focus:ring-indigo-500/20">
                </div>

                <!-- Color -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Project Color</label>
                    <div class="flex items-center gap-3">
                        @foreach(['#6366f1','#8b5cf6','#06b6d4','#10b981','#f59e0b','#ef4444','#ec4899','#f97316'] as $color)
                        <label>
                            <input type="radio" name="color" value="{{ $color }}" class="sr-only peer" {{ old('color', '#6366f1') === $color ? 'checked' : '' }}>
                            <span class="w-8 h-8 rounded-xl cursor-pointer block ring-2 ring-transparent peer-checked:ring-offset-2 peer-checked:ring-indigo-400 transition"
                                  style="background: {{ $color }}"></span>
                        </label>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Submit -->
            <div class="flex gap-4 pt-2">
                <a href="{{ route('projects.index') }}"
                   class="flex-1 py-3 rounded-xl border border-gray-200 dark:border-white/10 text-center text-sm text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-white/5 transition">
                    Cancel
                </a>
                <button type="submit"
                    class="flex-1 py-3 rounded-xl text-white text-sm font-medium shadow-lg transition-all hover:-translate-y-0.5"
                    style="background: linear-gradient(135deg, #6366f1, #8b5cf6)">
                    Create Project
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
