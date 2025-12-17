<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Client;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Eager load client
        $projects = Project::with('client')->latest()->get();
        // Pass clients for the Create Modal
        $clients = Client::all();
        return view('projects.index', compact('projects', 'clients'));
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
            'active' => 'boolean'
        ]);

        // Ensure active is boolean
        $validated['active'] = $request->has('active') ? true : false;
        if ($request->active == '1' || $request->active == 'true')
            $validated['active'] = true;

        $project = Project::create($validated);

        if ($request->ajax()) {
            return response()->json(['message' => 'Project created successfully', 'project' => $project]);
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
            'active' => 'boolean'
        ]);

        $validated['active'] = $request->has('active') ? true : false;
        if ($request->active == '1' || $request->active == 'true')
            $validated['active'] = true;

        $project->update($validated);

        if ($request->ajax()) {
            return response()->json(['message' => 'Project updated successfully', 'project' => $project]);
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
