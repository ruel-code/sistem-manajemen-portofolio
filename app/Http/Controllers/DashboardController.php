<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use App\Models\Invoice;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $workspace = session('current_workspace');

        if (!$workspace) {
            // Redirect to workspace setup if none selected
            $userWorkspaces = auth()->user()->workspaces()->with('projects')->get();
            if ($userWorkspaces->isEmpty()) {
                return redirect()->route('workspaces.create');
            }
            $workspace = $userWorkspaces->first();
            session(['current_workspace' => $workspace]);
        }

        // Dashboard statistics
        $stats = [
            'total_projects' => Project::where('workspace_id', $workspace->id)->count(),
            'active_projects' => Project::where('workspace_id', $workspace->id)->where('status', 'active')->count(),
            'total_tasks' => Task::where('workspace_id', $workspace->id)->count(),
            'completed_tasks' => Task::where('workspace_id', $workspace->id)->where('status', 'done')->count(),
            'overdue_tasks' => Task::where('workspace_id', $workspace->id)
                ->where('status', '!=', 'done')
                ->whereDate('due_date', '<', now())
                ->count(),
            'total_members' => $workspace->members()->count(),
            'total_invoices' => Invoice::where('workspace_id', $workspace->id)->count(),
            'revenue_total' => Invoice::where('workspace_id', $workspace->id)->where('status', 'paid')->sum('total'),
        ];

        // Recent projects
        $recentProjects = Project::where('workspace_id', $workspace->id)
            ->with(['manager', 'members'])
            ->latest()
            ->take(5)
            ->get();

        // My tasks (assigned to current user)
        $myTasks = Task::where('workspace_id', $workspace->id)
            ->where('assigned_to', auth()->id())
            ->where('status', '!=', 'done')
            ->with('project')
            ->orderBy('due_date')
            ->take(8)
            ->get();

        // Recent activities
        $recentActivities = ActivityLog::where('workspace_id', $workspace->id)
            ->with('user')
            ->latest()
            ->take(10)
            ->get();

        // Chart data: tasks by status
        $tasksByStatus = Task::where('workspace_id', $workspace->id)
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');

        // Chart data: projects by status
        $projectsByStatus = Project::where('workspace_id', $workspace->id)
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');

        // Chart data: tasks completed last 7 days
        $weeklyProgress = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $weeklyProgress[] = [
                'date' => $date->format('D'),
                'count' => Task::where('workspace_id', $workspace->id)
                    ->whereDate('completed_at', $date->toDateString())
                    ->count(),
            ];
        }

        return view('dashboard', compact(
            'workspace', 'stats', 'recentProjects',
            'myTasks', 'recentActivities', 'tasksByStatus',
            'projectsByStatus', 'weeklyProgress'
        ));
    }
}
