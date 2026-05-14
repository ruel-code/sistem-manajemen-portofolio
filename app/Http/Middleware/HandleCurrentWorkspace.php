<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Workspace;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class HandleCurrentWorkspace
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check()) {
            $workspace = session('current_workspace');

            // If workspace is an array or object, extract the ID
            $workspaceId = null;
            if (is_array($workspace)) {
                $workspaceId = $workspace['id'] ?? null;
            } elseif (is_object($workspace)) {
                $workspaceId = $workspace->id ?? null;
            }

            // Always try to get a fresh model
            if ($workspaceId) {
                $workspaceModel = Workspace::find($workspaceId);
            } else {
                $workspaceModel = auth()->user()->workspaces()->first();
            }

            // If still no workspace and not on the create page, redirect
            if (!$workspaceModel && !$request->routeIs('workspaces.*')) {
                return redirect()->route('workspaces.create');
            }

            // Save the fresh model back to session
            if ($workspaceModel) {
                session(['current_workspace' => $workspaceModel]);
            }
        }

        return $next($request);
    }
}
