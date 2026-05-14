@extends('layouts.app')
@section('title', $project->name . ' - Kanban')

@section('breadcrumb')
<div class="flex items-center gap-2 text-sm">
    <a href="{{ route('projects.index') }}" class="text-gray-400 hover:text-gray-600 transition">Projects</a>
    <span class="text-gray-300 dark:text-gray-600">/</span>
    <a href="{{ route('projects.show', $project) }}" class="text-gray-400 hover:text-gray-600 transition">{{ $project->name }}</a>
    <span class="text-gray-300 dark:text-gray-600">/</span>
    <span class="font-medium text-gray-700 dark:text-gray-200">Kanban</span>
</div>
@endsection

@section('content')
<div class="p-6 h-full flex flex-col">

    <!-- Header -->
    <div class="flex items-center justify-between mb-5">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-xl flex items-center justify-center text-white font-bold text-sm"
                 style="background: {{ $project->color ?? '#6366f1' }}">
                {{ substr($project->name, 0, 1) }}
            </div>
            <div>
                <h1 class="font-bold text-gray-900 dark:text-white">{{ $project->name }}</h1>
                <p class="text-xs text-gray-500">Kanban Board · Drag & Drop to reorder</p>
            </div>
        </div>
        <button onclick="document.getElementById('createTaskModal').classList.remove('hidden')"
                class="flex items-center gap-2 px-4 py-2 rounded-xl text-white text-sm font-medium"
                style="background: linear-gradient(135deg, #6366f1, #8b5cf6)">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Add Task
        </button>
    </div>

    <!-- Kanban Board -->
    <div class="flex gap-5 overflow-x-auto pb-4 flex-1">
        @foreach(\App\Models\Task::$statusColumns as $status => $label)
        <div class="flex-shrink-0 w-72">
            <!-- Column Header -->
            <div class="flex items-center justify-between mb-3">
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full
                        {{ $status === 'todo' ? 'bg-slate-400' :
                           ($status === 'in_progress' ? 'bg-indigo-500' :
                           ($status === 'review' ? 'bg-yellow-500' : 'bg-green-500')) }}">
                    </span>
                    <span class="font-semibold text-sm text-gray-700 dark:text-gray-200">{{ $label }}</span>
                    <span class="text-xs bg-gray-100 dark:bg-white/10 text-gray-500 dark:text-gray-400 px-2 py-0.5 rounded-full">
                        {{ count($tasksByStatus[$status]) }}
                    </span>
                </div>
            </div>

            <!-- Task Cards Container -->
            <div class="kanban-column space-y-3 min-h-20" id="column-{{ $status }}" data-status="{{ $status }}">
                @foreach($tasksByStatus[$status] as $task)
                <div class="task-card bg-white dark:bg-[#13132a] border border-gray-100 dark:border-white/5 rounded-xl p-4 shadow-sm"
                     data-task-id="{{ $task->id }}" data-status="{{ $task->status }}">
                    <!-- Priority & Labels -->
                    <div class="flex items-center gap-1.5 mb-2.5">
                        <span class="text-xs px-2 py-0.5 rounded-full font-medium
                            {{ $task->priority === 'urgent' ? 'bg-red-100 text-red-600 dark:bg-red-900/30 dark:text-red-400' :
                               ($task->priority === 'high' ? 'bg-orange-100 text-orange-600 dark:bg-orange-900/30 dark:text-orange-400' :
                               ($task->priority === 'medium' ? 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400' :
                               'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400')) }}">
                            {{ ucfirst($task->priority) }}
                        </span>
                        @foreach($task->labels->take(2) as $label)
                        <span class="text-xs px-2 py-0.5 rounded-full text-white" style="background: {{ $label->color }}">
                            {{ $label->name }}
                        </span>
                        @endforeach
                    </div>

                    <!-- Title -->
                    <a href="{{ route('tasks.show', $task) }}" class="block text-sm font-medium text-gray-800 dark:text-gray-200 hover:text-indigo-600 dark:hover:text-indigo-400 transition mb-2.5 leading-snug">
                        {{ $task->title }}
                    </a>

                    <!-- Checklist progress -->
                    @if($task->checklists->isNotEmpty())
                    <div class="mb-2.5">
                        <div class="flex items-center justify-between mb-1">
                            <span class="text-xs text-gray-400">
                                {{ $task->checklists->where('is_completed', true)->count() }}/{{ $task->checklists->count() }}
                            </span>
                        </div>
                        <div class="w-full bg-gray-100 dark:bg-white/10 rounded-full h-1">
                            <div class="h-1 rounded-full bg-indigo-500"
                                 style="width: {{ $task->checklist_progress }}%"></div>
                        </div>
                    </div>
                    @endif

                    <!-- Footer -->
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <!-- Attachments count -->
                            @if($task->attachments->isNotEmpty())
                            <span class="flex items-center gap-1 text-xs text-gray-400">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                                {{ $task->attachments->count() }}
                            </span>
                            @endif
                        </div>
                        <div class="flex items-center gap-2">
                            <!-- Due date -->
                            @if($task->due_date)
                            <span class="text-xs {{ $task->is_overdue ? 'text-red-500 font-medium' : 'text-gray-400' }}">
                                {{ $task->due_date->format('d/m') }}
                            </span>
                            @endif
                            <!-- Assignee -->
                            @if($task->assignee)
                            <img src="{{ $task->assignee->avatar_url }}" alt="{{ $task->assignee->name }}"
                                 class="w-6 h-6 rounded-full" title="{{ $task->assignee->name }}">
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Add task button -->
            <button onclick="openCreateTask('{{ $status }}')"
                    class="w-full mt-3 py-2 rounded-xl border-2 border-dashed border-gray-200 dark:border-white/10 text-gray-400 hover:text-indigo-500 hover:border-indigo-300 dark:hover:border-indigo-500/50 transition text-sm flex items-center justify-center gap-2">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Add Task
            </button>
        </div>
        @endforeach
    </div>
</div>

<!-- ==================== Create Task Modal ==================== -->
<div id="createTaskModal" class="hidden fixed inset-0 z-50 flex items-center justify-center">
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="closeModal()"></div>
    <div class="relative bg-white dark:bg-[#13132a] rounded-2xl shadow-2xl w-full max-w-lg mx-4 p-6">
        <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-5">Create New Task</h2>
        <form method="POST" action="{{ route('tasks.store', $project) }}" class="space-y-4">
            @csrf
            <input type="hidden" name="status" id="modalStatus" value="todo">

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Task Title *</label>
                <input type="text" name="title" required placeholder="Masukkan judul task..."
                    class="w-full rounded-xl border border-gray-200 dark:border-white/10 bg-white dark:bg-[#0f0f1a] text-gray-900 dark:text-gray-100 px-4 py-2.5 text-sm outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-400">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Description</label>
                <textarea name="description" rows="3" placeholder="Deskripsi task..."
                    class="w-full rounded-xl border border-gray-200 dark:border-white/10 bg-white dark:bg-[#0f0f1a] text-gray-900 dark:text-gray-100 px-4 py-2.5 text-sm outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-400 resize-none"></textarea>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Priority</label>
                    <select name="priority" class="w-full rounded-xl border border-gray-200 dark:border-white/10 bg-white dark:bg-[#0f0f1a] text-gray-900 dark:text-gray-100 px-3 py-2.5 text-sm outline-none focus:ring-2 focus:ring-indigo-500/20">
                        <option value="low">Low</option>
                        <option value="medium" selected>Medium</option>
                        <option value="high">High</option>
                        <option value="urgent">Urgent</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Due Date</label>
                    <input type="date" name="due_date"
                        class="w-full rounded-xl border border-gray-200 dark:border-white/10 bg-white dark:bg-[#0f0f1a] text-gray-900 dark:text-gray-100 px-3 py-2.5 text-sm outline-none focus:ring-2 focus:ring-indigo-500/20">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Assign To</label>
                <select name="assigned_to" class="w-full rounded-xl border border-gray-200 dark:border-white/10 bg-white dark:bg-[#0f0f1a] text-gray-900 dark:text-gray-100 px-3 py-2.5 text-sm outline-none focus:ring-2 focus:ring-indigo-500/20">
                    <option value="">Unassigned</option>
                    @foreach($project->members as $member)
                    <option value="{{ $member->id }}">{{ $member->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="button" onclick="closeModal()"
                    class="flex-1 py-2.5 rounded-xl border border-gray-200 dark:border-white/10 text-sm text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-white/5 transition">
                    Cancel
                </button>
                <button type="submit"
                    class="flex-1 py-2.5 rounded-xl text-white text-sm font-medium transition hover:-translate-y-0.5"
                    style="background: linear-gradient(135deg, #6366f1, #8b5cf6)">
                    Create Task
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
// ==================== Drag & Drop Kanban ====================
document.querySelectorAll('.kanban-column').forEach(column => {
    new Sortable(column, {
        group: 'kanban',
        animation: 150,
        ghostClass: 'sortable-ghost',
        onEnd: async function(evt) {
            const taskId = evt.item.dataset.taskId;
            const newStatus = evt.to.dataset.status;

            // Update status via AJAX
            try {
                await fetch(`/tasks/${taskId}/status`, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                    body: JSON.stringify({ status: newStatus }),
                });
                evt.item.dataset.status = newStatus;
            } catch (e) {
                console.error('Failed to update task status', e);
            }

            // Reorder all tasks in the column
            const tasks = [];
            evt.to.querySelectorAll('.task-card').forEach((card, index) => {
                tasks.push({ id: card.dataset.taskId, status: newStatus, order: index });
            });
            await fetch('/tasks/reorder', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                body: JSON.stringify({ tasks }),
            });
        }
    });
});

function openCreateTask(status) {
    document.getElementById('modalStatus').value = status;
    document.getElementById('createTaskModal').classList.remove('hidden');
}

function closeModal() {
    document.getElementById('createTaskModal').classList.add('hidden');
}
</script>
@endpush
