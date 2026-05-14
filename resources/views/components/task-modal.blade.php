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

<script>
function closeModal() {
    document.getElementById('createTaskModal').classList.add('hidden');
}
</script>
