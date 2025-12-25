<?php

namespace App\Http\Controllers;

use App\Http\Requests\Projects\StoreProjectRequest;
use App\Http\Requests\Projects\UpdateProjectRequest;
use App\Http\Resources\ProjectResource;
use App\Models\Client;
use App\Models\Project;
use App\Models\User;
use App\Services\ProjectService;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    protected ProjectService $projectService;

    public function __construct(ProjectService $projectService)
    {
        $this->projectService = $projectService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Eager load client and users (team)
        $projects = Project::withRelations()->latest()->get();
        // Pass clients for the Create Modal
        $clients = Client::all();

        // Fetch users grouped by role for assignment
        $allUsers = User::all();

        // Define all available roles (or fetch from users if dynamic, but static list is safer/cleaner)
        // Ideally should match the Seeder/System roles
        // Define all available roles (or fetch from users if dynamic, but static list is safer/cleaner)
        // Ideally should match the Seeder/System roles
        $systemRoles = \App\Enums\SystemRole::values();

        $usersByRole = [];
        foreach ($systemRoles as $role) {
            $usersByRole[$role] = $allUsers->filter(function ($user) use ($role) {
                return $user->hasRole($role);
            });
        }

        $roles = $systemRoles;
        return view('projects.index', compact('projects', 'clients', 'usersByRole', 'roles'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return redirect()->route('projects.index');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProjectRequest $request)
    {
        $project = $this->projectService->createProject($request->validated());

        if ($request->ajax()) {
            return response()->json([
                'message' => 'Project created successfully',
                'project' => ProjectResource::make($project->load('users'))->resolve()
            ]);
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
            return response()->json(ProjectResource::make($project)->resolve());
        }
        $clients = Client::all();
        return view('projects.edit', compact('project', 'clients'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProjectRequest $request, Project $project)
    {
        $updatedProject = $this->projectService->updateProject($project, $request->validated());

        if ($request->ajax()) {
            return response()->json([
                'message' => 'Project updated successfully',
                'project' => ProjectResource::make($updatedProject->load('users'))->resolve()
            ]);
        }

        return redirect()->route('projects.index')->with('success', 'Project updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Project $project)
    {
        $this->projectService->deleteProject($project);

        if (request()->ajax()) {
            return response()->json(['message' => 'Project deleted successfully']);
        }

        return redirect()->route('projects.index')->with('success', 'Project deleted successfully.');
    }
}
