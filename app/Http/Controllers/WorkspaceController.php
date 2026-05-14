<?php

namespace App\Http\Controllers;

use App\Models\Workspace;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class WorkspaceController extends Controller
{
    public function index()
    {
        $workspaces = auth()->user()->workspaces()->with('owner', 'projects')->get()
            ->merge(auth()->user()->ownedWorkspaces()->with('projects')->get())
            ->unique('id');

        return view('workspaces.index', compact('workspaces'));
    }

    public function create()
    {
        return view('workspaces.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'color' => 'nullable|string|max:20',
        ]);

        $workspace = Workspace::create([
            ...$validated,
            'owner_id' => auth()->id(),
            'slug' => Str::slug($validated['name']) . '-' . Str::random(6),
        ]);

        // Add owner as member
        $workspace->members()->attach(auth()->id(), [
            'role' => 'owner',
            'joined_at' => now(),
        ]);

        // Log activity
        ActivityLog::create([
            'user_id' => auth()->id(),
            'workspace_id' => $workspace->id,
            'action' => 'workspace_created',
            'description' => "Workspace '{$workspace->name}' dibuat",
        ]);

        session(['current_workspace' => $workspace]);

        return redirect()->route('dashboard')
            ->with('success', "Workspace '{$workspace->name}' berhasil dibuat!");
    }

    public function switch(Workspace $workspace)
    {
        // Check if user is a member
        $isMember = $workspace->members()->where('user_id', auth()->id())->exists()
            || $workspace->owner_id === auth()->id();

        if (!$isMember) {
            abort(403, 'Anda tidak memiliki akses ke workspace ini.');
        }

        session(['current_workspace' => $workspace]);

        return redirect()->route('dashboard')
            ->with('success', "Switched to '{$workspace->name}'");
    }

    public function show(Workspace $workspace)
    {
        $this->authorize('view', $workspace);
        $workspace->load('members', 'projects', 'owner');
        return view('workspaces.show', compact('workspace'));
    }

    public function edit(Workspace $workspace)
    {
        $this->authorize('update', $workspace);
        return view('workspaces.edit', compact('workspace'));
    }

    public function update(Request $request, Workspace $workspace)
    {
        $this->authorize('update', $workspace);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'color' => 'nullable|string|max:20',
        ]);

        $workspace->update($validated);

        return back()->with('success', 'Workspace berhasil diperbarui!');
    }

    public function destroy(Workspace $workspace)
    {
        $this->authorize('delete', $workspace);
        $workspace->delete();
        session()->forget('current_workspace');

        return redirect()->route('workspaces.index')
            ->with('success', 'Workspace berhasil dihapus.');
    }

    public function invite(Request $request, Workspace $workspace)
    {
        $this->authorize('update', $workspace);

        $request->validate([
            'email' => 'required|email|exists:users,email',
            'role' => 'required|in:admin,member,client',
        ]);

        $user = \App\Models\User::where('email', $request->email)->first();

        if ($workspace->members()->where('user_id', $user->id)->exists()) {
            return back()->with('error', 'User sudah menjadi member!');
        }

        $workspace->members()->attach($user->id, [
            'role' => $request->role,
            'joined_at' => now(),
        ]);

        return back()->with('success', "{$user->name} berhasil diundang ke workspace!");
    }
}
