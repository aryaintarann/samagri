<?php

namespace App\Http\Controllers;

use App\Models\Sop;
use Illuminate\Http\Request;

class SopController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $sops = Sop::with('creator')->latest()->get();
        return view('sops.index', compact('sops'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Only CEO can create
        if (auth()->user()->role !== 'CEO') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        $validated['created_by'] = auth()->id();

        $sop = Sop::create($validated);

        if ($request->ajax()) {
            return response()->json(['message' => 'SOP created successfully', 'sop' => $sop]);
        }

        return redirect()->route('sops.index')->with('success', 'SOP created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Sop $sop)
    {
        if (request()->ajax()) {
            return response()->json($sop);
        }
        return view('sops.show', compact('sop'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Sop $sop)
    {
        // Only CEO
        if (auth()->user()->role !== 'CEO') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        if (request()->ajax()) {
            return response()->json($sop);
        }
        return view('sops.edit', compact('sop'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Sop $sop)
    {
        if (auth()->user()->role !== 'CEO') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        $sop->update($validated);

        if ($request->ajax()) {
            return response()->json(['message' => 'SOP updated successfully', 'sop' => $sop]);
        }

        return redirect()->route('sops.index')->with('success', 'SOP updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Sop $sop)
    {
        if (auth()->user()->role !== 'CEO') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $sop->delete();

        if (request()->ajax()) {
            return response()->json(['message' => 'SOP deleted successfully']);
        }

        return redirect()->route('sops.index')->with('success', 'SOP deleted successfully.');
    }
}
