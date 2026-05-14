<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProjectController extends Controller
{
    public function index(Request $request)
    {
        $workspace = session('current_workspace');
        abort_if(!$workspace, 404, 'No workspace selected.');

        $query = Project::where('workspace_id', $workspace->id)
            ->with(['manager', 'members', 'tasks']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $projects = $query->latest()->paginate(12);

        return view('projects.index', compact('projects', 'workspace'));
    }

    public function create()
    {
        $workspace = session('current_workspace');
        $members = $workspace->members;
        return view('projects.create', compact('workspace', 'members'));
    }

    public function store(Request $request)
    {
        $workspace = session('current_workspace');

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:planning,active,review,completed,on_hold',
            'priority' => 'required|in:low,medium,high,urgent',
            'start_date' => 'nullable|date',
            'due_date' => 'nullable|date|after_or_equal:start_date',
            'manager_id' => 'nullable|exists:users,id',
            'client_id' => 'nullable|exists:users,id',
            'budget' => 'nullable|numeric|min:0',
            'color' => 'nullable|string|max:20',
            'members' => 'nullable|array',
            'members.*' => 'exists:users,id',
        ]);

        $project = Project::create([
            ...$validated,
            'workspace_id' => $workspace->id,
            'created_by' => auth()->id(),
        ]);

        // Attach members
        if (!empty($validated['members'])) {
            foreach ($validated['members'] as $memberId) {
                $project->members()->attach($memberId, ['role' => 'member']);
            }
        }

        ActivityLog::create([
            'user_id' => auth()->id(),
            'workspace_id' => $workspace->id,
            'action' => 'project_created',
            'description' => "Project '{$project->name}' dibuat",
            'model_type' => Project::class,
            'model_id' => $project->id,
        ]);

        return redirect()->route('projects.show', $project)
            ->with('success', "Project '{$project->name}' berhasil dibuat!");
    }

    public function show(Project $project)
    {
        $project->load(['manager', 'client', 'members', 'tasks.assignee', 'activities.user']);

        // Tasks grouped by status for kanban
        $tasksByStatus = [];
        foreach (Task::$statusColumns as $status => $label) {
            $tasksByStatus[$status] = $project->tasks()
                ->where('status', $status)
                ->whereNull('parent_id')
                ->with(['assignee', 'labels', 'checklists'])
                ->orderBy('order')
                ->get();
        }

        $stats = [
            'total' => $project->tasks()->count(),
            'done' => $project->tasks()->where('status', 'done')->count(),
            'in_progress' => $project->tasks()->where('status', 'in_progress')->count(),
            'overdue' => $project->tasks()->where('status', '!=', 'done')
                ->whereDate('due_date', '<', now())->count(),
        ];

        return view('projects.show', compact('project', 'tasksByStatus', 'stats'));
    }

    public function edit(Project $project)
    {
        $workspace = session('current_workspace');
        $members = $workspace->members;
        return view('projects.edit', compact('project', 'workspace', 'members'));
    }

    public function update(Request $request, Project $project)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:planning,active,review,completed,on_hold',
            'priority' => 'required|in:low,medium,high,urgent',
            'start_date' => 'nullable|date',
            'due_date' => 'nullable|date',
            'manager_id' => 'nullable|exists:users,id',
            'budget' => 'nullable|numeric|min:0',
            'color' => 'nullable|string|max:20',
        ]);

        $project->update($validated);
        $project->update(['progress' => $project->calculateProgress()]);

        return back()->with('success', 'Project berhasil diperbarui!');
    }

    public function destroy(Project $project)
    {
        $project->delete();
        return redirect()->route('projects.index')
            ->with('success', 'Project berhasil dihapus.');
    }

    public function kanban(Project $project)
    {
        $tasksByStatus = [];
        foreach (Task::$statusColumns as $status => $label) {
            $tasksByStatus[$status] = $project->tasks()
                ->where('status', $status)
                ->whereNull('parent_id')
                ->with(['assignee', 'labels', 'checklists', 'attachments'])
                ->orderBy('order')
                ->get();
        }
        return view('projects.kanban', compact('project', 'tasksByStatus'));
    }
}
