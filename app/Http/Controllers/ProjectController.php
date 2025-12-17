<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Client;
use App\Models\User;
use Illuminate\Http\Request;
use App\Notifications\ProjectAssigned;
use Illuminate\Support\Facades\Notification;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Eager load client and users (team)
        $projects = Project::with(['client', 'users'])->latest()->get();
        // Pass clients for the Create Modal
        $clients = Client::all();
        // Fetch users grouped by role for assignment
        $usersByRole = User::all()->groupBy('role');
        $roles = User::select('role')->distinct()->pluck('role');
        return view('projects.index', compact('projects', 'clients', 'usersByRole', 'roles'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Projects are created via Modal on the Index page
        // Redirecting to index to trigger the modal viewing logic if we wanted, 
        // or simply show the index page where the 'Add Project' button is visible.
        return redirect()->route('projects.index');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'client_id' => 'required|exists:clients,id',
            'status' => 'required|string',
            'deadline' => 'nullable|date',
            'budget' => 'nullable|numeric',
            'description' => 'nullable|string',
            'active' => 'boolean',
            'user_id' => 'nullable|exists:users,id',
        ]);

        // Ensure active is boolean
        $validated['active'] = $request->has('active') ? true : false;
        if ($request->active == '1' || $request->active == 'true')
            $validated['active'] = true;

        $project = Project::create($validated);

        // Sync Assignees (Team)
        if ($request->has('assignees')) {
            // Filter out empty values
            $assignees = array_filter($request->assignees);
            $project->users()->sync($assignees);

            // Notify assigned users
            $users = User::whereIn('id', $assignees)->get();
            Notification::send($users, new ProjectAssigned($project));
        }

        if ($request->ajax()) {
            return response()->json(['message' => 'Project created successfully', 'project' => $project->load('users')]);
        }

        return redirect()->route('projects.index')->with('success', 'Project created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Project $project)
    {
        return view('projects.show', compact('project'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Project $project)
    {
        if (request()->ajax()) {
            return response()->json($project);
        }
        $clients = Client::all();
        return view('projects.edit', compact('project', 'clients'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Project $project)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'client_id' => 'required|exists:clients,id',
            'status' => 'required|string',
            'deadline' => 'nullable|date',
            'budget' => 'nullable|numeric',
            'description' => 'nullable|string',
            'active' => 'boolean',
            'user_id' => 'nullable|exists:users,id',
        ]);

        $validated['active'] = $request->has('active') ? true : false;
        if ($request->active == '1' || $request->active == 'true')
            $validated['active'] = true;

        $project->update($validated);

        // Sync Assignees (Team)
        if ($request->has('assignees')) {
            $assignees = array_filter($request->assignees);

            // Notify only new assignees (optional logic, but simple is notify all on change)
            // Let's notify newly attached ones for better UX.
            $currentIds = $project->users()->pluck('user_id')->toArray();
            $newIds = array_diff($assignees, $currentIds);

            $project->users()->sync($assignees);

            if (!empty($newIds)) {
                $users = User::whereIn('id', $newIds)->get();
                Notification::send($users, new ProjectAssigned($project));
            }
        }

        if ($request->ajax()) {
            return response()->json(['message' => 'Project updated successfully', 'project' => $project->load('users')]);
        }

        return redirect()->route('projects.index')->with('success', 'Project updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Project $project)
    {
        $project->delete();

        if (request()->ajax()) {
            return response()->json(['message' => 'Project deleted successfully']);
        }

        return redirect()->route('projects.index')->with('success', 'Project deleted successfully.');
    }
}
