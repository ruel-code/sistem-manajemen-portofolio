<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\WorkspaceController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

// Redirect root to dashboard
Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
});

// Auth routes (Breeze)
require __DIR__.'/auth.php';

// Protected routes
Route::middleware(['auth', 'verified'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profile (Breeze default)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Workspaces
    Route::resource('workspaces', WorkspaceController::class);
    Route::post('/workspaces/{workspace}/switch', [WorkspaceController::class, 'switch'])->name('workspaces.switch');
    Route::post('/workspaces/{workspace}/invite', [WorkspaceController::class, 'invite'])->name('workspaces.invite');

    // Projects
    Route::resource('projects', ProjectController::class);
    Route::get('/projects/{project}/kanban', [ProjectController::class, 'kanban'])->name('projects.kanban');

    // Tasks
    Route::post('/projects/{project}/tasks', [TaskController::class, 'store'])->name('tasks.store');
    Route::get('/tasks/{task}', [TaskController::class, 'show'])->name('tasks.show');
    Route::patch('/tasks/{task}', [TaskController::class, 'update'])->name('tasks.update');
    Route::patch('/tasks/{task}/status', [TaskController::class, 'updateStatus'])->name('tasks.update-status');
    Route::post('/tasks/reorder', [TaskController::class, 'reorder'])->name('tasks.reorder');
    Route::delete('/tasks/{task}', [TaskController::class, 'destroy'])->name('tasks.destroy');

    // Task sub-resources
    Route::post('/tasks/{task}/comments', [TaskController::class, 'addComment'])->name('tasks.comments.store');
    Route::post('/tasks/{task}/checklists', [TaskController::class, 'addChecklist'])->name('tasks.checklists.store');
    Route::patch('/checklists/{checklist}/toggle', [TaskController::class, 'toggleChecklist'])->name('checklists.toggle');
    Route::post('/tasks/{task}/attachments', [TaskController::class, 'uploadAttachment'])->name('tasks.attachments.store');

    // Invoices
    Route::resource('invoices', InvoiceController::class);
    Route::get('/invoices/{invoice}/pdf', [InvoiceController::class, 'pdf'])->name('invoices.pdf');
    Route::patch('/invoices/{invoice}/status', [InvoiceController::class, 'updateStatus'])->name('invoices.status');

    // Calendar
    Route::get('/calendar', function () {
        $workspace = session('current_workspace');
        $workspaceId = is_array($workspace) ? $workspace['id'] : $workspace?->id;
        $tasks = \App\Models\Task::where('workspace_id', $workspaceId)
            ->whereNotNull('due_date')
            ->with('project')
            ->get();
        $projects = \App\Models\Project::where('workspace_id', $workspaceId)
            ->whereNotNull('due_date')
            ->get();
        return view('calendar.index', compact('tasks', 'projects'));
    })->name('calendar');

    // Files
    Route::get('/files', function () {
        $workspace = session('current_workspace');
        $workspaceId = is_array($workspace) ? $workspace['id'] : $workspace?->id;
        $files = \App\Models\File::where('workspace_id', $workspaceId)
            ->with('user', 'project')
            ->latest()
            ->paginate(20);
        return view('files.index', compact('files'));
    })->name('files.index');

    Route::post('/files/upload', function (\Illuminate\Http\Request $request) {
        $request->validate(['file' => 'required|file|max:20480', 'project_id' => 'nullable|exists:projects,id']);
        $workspace = session('current_workspace');
        $workspaceId = is_array($workspace) ? $workspace['id'] : $workspace?->id;
        $file = $request->file('file');
        $path = $file->store("workspace/{$workspaceId}/files", 'public');

        $fileModel = \App\Models\File::create([
            'workspace_id' => $workspaceId,
            'project_id' => $request->project_id,
            'user_id' => auth()->id(),
            'name' => $file->getClientOriginalName(),
            'original_name' => $file->getClientOriginalName(),
            'path' => $path,
            'mime_type' => $file->getMimeType(),
            'size' => $file->getSize(),
        ]);

        return response()->json(['success' => true, 'file' => $fileModel]);
    })->name('files.upload');

    // Chat
    Route::get('/chat', function () {
        $workspace = session('current_workspace');
        $workspaceId = is_array($workspace) ? $workspace['id'] : $workspace?->id;
        $channels = \App\Models\ChatChannel::where('workspace_id', $workspaceId)
            ->with('lastMessage.user', 'members')
            ->get();
        return view('chat.index', compact('channels'));
    })->name('chat.index');

    // API routes for AJAX
    Route::prefix('api')->group(function () {
        Route::get('/workspace/stats', function () {
            $workspace = session('current_workspace');
            $workspaceId = is_array($workspace) ? $workspace['id'] : $workspace?->id;
            return response()->json([
                'projects' => \App\Models\Project::where('workspace_id', $workspaceId)->count(),
                'tasks' => \App\Models\Task::where('workspace_id', $workspaceId)->count(),
            ]);
        });

        Route::get('/tasks/calendar', function () {
            $workspace = session('current_workspace');
            $workspaceId = is_array($workspace) ? $workspace['id'] : $workspace?->id;
            $tasks = \App\Models\Task::where('workspace_id', $workspaceId)
                ->whereNotNull('due_date')
                ->with('project')
                ->get()
                ->map(fn($task) => [
                    'id' => $task->id,
                    'title' => $task->title,
                    'start' => $task->due_date->toDateString(),
                    'color' => match($task->priority) {
                        'urgent' => '#ef4444',
                        'high' => '#f97316',
                        'medium' => '#6366f1',
                        default => '#64748b',
                    },
                    'url' => route('tasks.show', $task),
                ]);
            return response()->json($tasks);
        });
    });
});
