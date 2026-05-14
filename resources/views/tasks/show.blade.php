@extends('layouts.app')
@section('title', $task->title)

@section('breadcrumb')
<div class="flex items-center gap-2 text-sm">
    <a href="{{ route('projects.show', $task->project) }}" class="text-gray-400 hover:text-gray-600 transition">{{ $task->project->name }}</a>
    <span class="text-gray-300 dark:text-gray-600">/</span>
    <span class="font-medium text-gray-700 dark:text-gray-200">Task Details</span>
</div>
@endsection

@section('content')
<div class="p-6 space-y-6" x-data="taskDetails()">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left: Task Info & Content -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white dark:bg-[#13132a] border border-gray-100 dark:border-white/5 rounded-2xl p-8 shadow-sm">
                <!-- Status & Priority badges -->
                <div class="flex items-center gap-3 mb-6">
                    <select @change="updateStatus($event.target.value)"
                        class="text-xs font-bold uppercase tracking-widest px-3 py-1.5 rounded-lg border-none outline-none focus:ring-2 focus:ring-indigo-500/20
                        {{ $task->status === 'done' ? 'bg-green-100 text-green-700 dark:bg-green-900/30' : 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900/30' }}">
                        @foreach(\App\Models\Task::$statusColumns as $status => $label)
                        <option value="{{ $status }}" {{ $task->status === $status ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    <span class="text-xs font-bold uppercase tracking-widest px-3 py-1.5 rounded-lg
                        {{ $task->priority === 'urgent' ? 'bg-red-100 text-red-700 dark:bg-red-900/30' : 'bg-gray-100 text-gray-700 dark:bg-gray-800' }}">
                        {{ $task->priority }}
                    </span>
                </div>

                <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-4">{{ $task->title }}</h1>
                <div class="prose dark:prose-invert max-w-none text-gray-600 dark:text-gray-400">
                    {!! nl2br(e($task->description)) ?? '<p class="italic text-gray-400">No description provided.</p>' !!}
                </div>

                <!-- Labels -->
                @if($task->labels->isNotEmpty())
                <div class="flex flex-wrap gap-2 mt-8">
                    @foreach($task->labels as $label)
                    <span class="px-3 py-1 rounded-full text-xs font-medium text-white" style="background: {{ $label->color }}">
                        {{ $label->name }}
                    </span>
                    @endforeach
                </div>
                @endif
            </div>

            <!-- Checklists -->
            <div class="bg-white dark:bg-[#13132a] border border-gray-100 dark:border-white/5 rounded-2xl p-8 shadow-sm">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="font-bold text-gray-900 dark:text-white flex items-center gap-2">
                        <svg class="w-5 h-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                        Checklist
                    </h3>
                    <div class="text-xs text-gray-400 font-medium" x-text="`${checklistProgress}% completed`"></div>
                </div>
                <div class="w-full bg-gray-100 dark:bg-white/5 h-2 rounded-full mb-6">
                    <div class="h-2 bg-indigo-500 rounded-full transition-all duration-500" :style="`width: ${checklistProgress}%`"></div>
                </div>

                <div class="space-y-3">
                    @foreach($task->checklists as $item)
                    <div class="flex items-center gap-3 group">
                        <input type="checkbox" @change="toggleChecklist({{ $item->id }})" {{ $item->is_completed ? 'checked' : '' }}
                               class="w-5 h-5 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 dark:bg-[#0f0f1a] dark:border-white/10">
                        <span class="text-sm {{ $item->is_completed ? 'line-through text-gray-400' : 'text-gray-700 dark:text-gray-300' }}">
                            {{ $item->title }}
                        </span>
                    </div>
                    @endforeach
                    <div class="pt-2">
                        <input type="text" x-model="newChecklist" @keydown.enter="addChecklist()" placeholder="Add an item..."
                               class="w-full bg-transparent border-none text-sm text-gray-600 dark:text-gray-400 placeholder-gray-400 focus:ring-0 px-0">
                    </div>
                </div>
            </div>

            <!-- Comments -->
            <div class="bg-white dark:bg-[#13132a] border border-gray-100 dark:border-white/5 rounded-2xl p-8 shadow-sm">
                <h3 class="font-bold text-gray-900 dark:text-white mb-8 flex items-center gap-2">
                    <svg class="w-5 h-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                    Comments
                </h3>

                <div class="space-y-8 mb-8">
                    @foreach($task->comments as $comment)
                    <div class="flex gap-4">
                        <img src="{{ $comment->user->avatar_url }}" class="w-10 h-10 rounded-full flex-shrink-0">
                        <div class="flex-1">
                            <div class="bg-gray-50 dark:bg-white/5 rounded-2xl p-4">
                                <div class="flex items-center justify-between mb-1">
                                    <span class="text-sm font-bold text-gray-900 dark:text-white">{{ $comment->user->name }}</span>
                                    <span class="text-[10px] text-gray-400 font-bold uppercase tracking-wider">{{ $comment->created_at->diffForHumans() }}</span>
                                </div>
                                <p class="text-sm text-gray-600 dark:text-gray-300 leading-relaxed">{{ $comment->content }}</p>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

                <div class="flex gap-4">
                    <img src="{{ auth()->user()->avatar_url }}" class="w-10 h-10 rounded-full flex-shrink-0">
                    <div class="flex-1">
                        <textarea x-model="commentText" placeholder="Write a comment..." rows="3"
                            class="w-full rounded-2xl border border-gray-100 dark:border-white/5 bg-gray-50 dark:bg-white/5 text-sm p-4 focus:ring-2 focus:ring-indigo-500/20 outline-none resize-none"></textarea>
                        <div class="flex justify-end mt-2">
                            <button @click="postComment()" class="px-6 py-2 bg-indigo-500 text-white text-sm font-bold rounded-xl shadow-lg hover:bg-indigo-600 transition">Post Comment</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Sidebar: Meta Data -->
        <div class="space-y-6">
            <!-- Details Card -->
            <div class="bg-white dark:bg-[#13132a] border border-gray-100 dark:border-white/5 rounded-2xl p-6 shadow-sm">
                <h4 class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-6">Task Details</h4>
                <div class="space-y-5">
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-gray-400">Assigned To</span>
                        <div class="flex items-center gap-2">
                            @if($task->assignee)
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-200">{{ $task->assignee->name }}</span>
                            <img src="{{ $task->assignee->avatar_url }}" class="w-6 h-6 rounded-full">
                            @else
                            <span class="text-xs text-gray-400 italic">Unassigned</span>
                            @endif
                        </div>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-gray-400">Due Date</span>
                        <span class="text-sm font-medium {{ $task->is_overdue ? 'text-red-500' : 'text-gray-700 dark:text-gray-200' }}">
                            {{ $task->due_date ? $task->due_date->format('d M Y') : '-' }}
                        </span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-gray-400">Project</span>
                        <a href="{{ route('projects.show', $task->project) }}" class="text-sm font-medium text-indigo-500 hover:underline">
                            {{ $task->project->name }}
                        </a>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-gray-400">Estimate</span>
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-200">{{ $task->estimated_hours ?? 0 }} hrs</span>
                    </div>
                </div>
            </div>

            <!-- Attachments Card -->
            <div class="bg-white dark:bg-[#13132a] border border-gray-100 dark:border-white/5 rounded-2xl p-6 shadow-sm">
                <div class="flex items-center justify-between mb-6">
                    <h4 class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Attachments</h4>
                    <label class="p-1 text-indigo-500 hover:bg-indigo-50 dark:hover:bg-white/5 rounded cursor-pointer transition">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        <input type="file" class="hidden" @change="uploadFile($event)">
                    </label>
                </div>
                <div class="space-y-3">
                    @forelse($task->attachments as $file)
                    <div class="flex items-center gap-3 p-2 rounded-xl bg-gray-50 dark:bg-white/5 group">
                        <div class="w-8 h-8 rounded-lg bg-indigo-500/10 text-indigo-500 flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-xs font-bold text-gray-700 dark:text-gray-200 truncate">{{ $file->name }}</p>
                            <p class="text-[10px] text-gray-400">{{ $file->formatted_size }}</p>
                        </div>
                        <a href="{{ $file->url }}" download class="p-1 opacity-0 group-hover:opacity-100 text-gray-400 hover:text-indigo-500 transition">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                        </a>
                    </div>
                    @empty
                    <p class="text-xs text-gray-400 text-center py-4 italic">No attachments yet.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function taskDetails() {
    return {
        checklistProgress: {{ $task->checklist_progress }},
        commentText: '',
        newChecklist: '',

        async updateStatus(status) {
            try {
                const res = await fetch('{{ route("tasks.update-status", $task) }}', {
                    method: 'PATCH',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                    body: JSON.stringify({ status })
                });
                if (res.ok) showToast('Status updated!');
            } catch (e) { showToast('Update failed', 'error'); }
        },

        async toggleChecklist(id) {
            try {
                const res = await fetch(`/checklists/${id}/toggle`, {
                    method: 'PATCH',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken }
                });
                const data = await res.json();
                if (data.success) {
                    this.checklistProgress = data.progress;
                    showToast('Checklist updated');
                }
            } catch (e) { showToast('Toggle failed', 'error'); }
        },

        async addChecklist() {
            if (!this.newChecklist.trim()) return;
            try {
                const res = await fetch('{{ route("tasks.checklists.store", $task) }}', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                    body: JSON.stringify({ title: this.newChecklist })
                });
                if (res.ok) location.reload();
            } catch (e) { showToast('Add failed', 'error'); }
        },

        async postComment() {
            if (!this.commentText.trim()) return;
            try {
                const res = await fetch('{{ route("tasks.comments.store", $task) }}', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                    body: JSON.stringify({ content: this.commentText })
                });
                if (res.ok) location.reload();
            } catch (e) { showToast('Comment failed', 'error'); }
        },

        async uploadFile(event) {
            const file = event.target.files[0];
            if (!file) return;
            const formData = new FormData();
            formData.append('file', file);
            formData.append('_token', csrfToken);

            try {
                const res = await fetch('{{ route("tasks.attachments.store", $task) }}', {
                    method: 'POST',
                    body: formData
                });
                if (res.ok) {
                    showToast('File uploaded!');
                    location.reload();
                }
            } catch (e) { showToast('Upload failed', 'error'); }
        }
    }
}
</script>
@endpush
