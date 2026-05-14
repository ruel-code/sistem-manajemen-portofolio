<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Project;
use App\Models\TaskComment;
use App\Models\TaskChecklist;
use App\Models\TaskAttachment;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function store(Request $request, Project $project)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:todo,in_progress,review,done',
            'priority' => 'required|in:low,medium,high,urgent',
            'due_date' => 'nullable|date',
            'assigned_to' => 'nullable|exists:users,id',
            'parent_id' => 'nullable|exists:tasks,id',
            'estimated_hours' => 'nullable|integer',
        ]);

        $task = Task::create([
            ...$validated,
            'project_id' => $project->id,
            'workspace_id' => $project->workspace_id,
            'created_by' => auth()->id(),
            'order' => Task::where('project_id', $project->id)
                ->where('status', $validated['status'])->count(),
        ]);

        // Update project progress
        $project->update(['progress' => $project->calculateProgress()]);

        ActivityLog::create([
            'user_id' => auth()->id(),
            'workspace_id' => $project->workspace_id,
            'action' => 'task_created',
            'description' => "Task '{$task->title}' dibuat di project '{$project->name}'",
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'task' => $task->load(['assignee', 'labels', 'checklists']),
            ]);
        }

        return back()->with('success', 'Task berhasil dibuat!');
    }

    public function show(Task $task)
    {
        $task->load(['project', 'assignee', 'creator', 'labels', 'checklists', 'attachments',
            'comments.user', 'comments.replies.user', 'subtasks.assignee']);
        return view('tasks.show', compact('task'));
    }

    public function update(Request $request, Task $task)
    {
        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'sometimes|in:todo,in_progress,review,done',
            'priority' => 'sometimes|in:low,medium,high,urgent',
            'due_date' => 'nullable|date',
            'assigned_to' => 'nullable|exists:users,id',
            'estimated_hours' => 'nullable|integer',
        ]);

        if (isset($validated['status']) && $validated['status'] === 'done' && $task->status !== 'done') {
            $validated['completed_at'] = now();
        }

        $task->update($validated);

        // Recalculate project progress
        $task->project->update(['progress' => $task->project->calculateProgress()]);

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'task' => $task->fresh()]);
        }

        return back()->with('success', 'Task berhasil diperbarui!');
    }

    public function updateStatus(Request $request, Task $task)
    {
        $request->validate(['status' => 'required|in:todo,in_progress,review,done']);

        $old = $task->status;
        $task->update([
            'status' => $request->status,
            'completed_at' => $request->status === 'done' ? now() : null,
        ]);

        $task->project->update(['progress' => $task->project->calculateProgress()]);

        return response()->json(['success' => true, 'progress' => $task->project->progress]);
    }

    public function reorder(Request $request)
    {
        $request->validate([
            'tasks' => 'required|array',
            'tasks.*.id' => 'required|exists:tasks,id',
            'tasks.*.status' => 'required|string',
            'tasks.*.order' => 'required|integer',
        ]);

        foreach ($request->tasks as $taskData) {
            Task::where('id', $taskData['id'])->update([
                'status' => $taskData['status'],
                'order' => $taskData['order'],
            ]);
        }

        return response()->json(['success' => true]);
    }

    public function destroy(Task $task)
    {
        $project = $task->project;
        $task->delete();
        $project->update(['progress' => $project->calculateProgress()]);

        if (request()->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', 'Task berhasil dihapus.');
    }

    public function addComment(Request $request, Task $task)
    {
        $request->validate(['content' => 'required|string', 'parent_id' => 'nullable|exists:task_comments,id']);

        $comment = TaskComment::create([
            'task_id' => $task->id,
            'user_id' => auth()->id(),
            'content' => $request->content,
            'parent_id' => $request->parent_id,
        ]);

        return response()->json([
            'success' => true,
            'comment' => $comment->load('user'),
        ]);
    }

    public function toggleChecklist(TaskChecklist $checklist)
    {
        $checklist->update([
            'is_completed' => !$checklist->is_completed,
            'completed_by' => !$checklist->is_completed ? auth()->id() : null,
            'completed_at' => !$checklist->is_completed ? now() : null,
        ]);

        return response()->json([
            'success' => true,
            'is_completed' => $checklist->is_completed,
            'progress' => $checklist->task->checklist_progress,
        ]);
    }

    public function addChecklist(Request $request, Task $task)
    {
        $request->validate(['title' => 'required|string|max:255']);

        $checklist = TaskChecklist::create([
            'task_id' => $task->id,
            'title' => $request->title,
            'order' => $task->checklists()->count(),
        ]);

        return response()->json(['success' => true, 'checklist' => $checklist]);
    }

    public function uploadAttachment(Request $request, Task $task)
    {
        $request->validate(['file' => 'required|file|max:10240']);

        $file = $request->file('file');
        $path = $file->store("tasks/{$task->id}", 'public');

        $attachment = TaskAttachment::create([
            'task_id' => $task->id,
            'user_id' => auth()->id(),
            'name' => $file->getClientOriginalName(),
            'path' => $path,
            'mime_type' => $file->getMimeType(),
            'size' => $file->getSize(),
        ]);

        return response()->json(['success' => true, 'attachment' => $attachment]);
    }
}
