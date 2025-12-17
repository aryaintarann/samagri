<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Project;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $invoices = Invoice::with('project.client')->latest()->get();
        $projects = Project::where('active', true)->get(); // Only active projects for new invoices
        return view('invoices.index', compact('invoices', 'projects'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'amount' => 'required|numeric',
            'status' => 'required|string',
            'due_date' => 'nullable|date',
        ]);

        // Generate Invoice Number (Simple logic)
        $validated['invoice_number'] = 'INV-' . time();

        $invoice = Invoice::create($validated);

        if ($request->ajax()) {
            return response()->json(['message' => 'Invoice created successfully', 'invoice' => $invoice]);
        }

        return redirect()->route('invoices.index')->with('success', 'Invoice created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Invoice $invoice)
    {
        if (request()->ajax()) {
            return response()->json($invoice);
        }
        return view('invoices.show', compact('invoice'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Invoice $invoice)
    {
        if (request()->ajax()) {
            return response()->json($invoice);
        }
        $projects = Project::all();
        return view('invoices.edit', compact('invoice', 'projects'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Invoice $invoice)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'amount' => 'required|numeric',
            'status' => 'required|string',
            'due_date' => 'nullable|date',
        ]);

        $invoice->update($validated);

        if ($request->ajax()) {
            return response()->json(['message' => 'Invoice updated successfully', 'invoice' => $invoice]);
        }

        return redirect()->route('invoices.index')->with('success', 'Invoice updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Invoice $invoice)
    {
        $invoice->delete();

        if (request()->ajax()) {
            return response()->json(['message' => 'Invoice deleted successfully']);
        }

        return redirect()->route('invoices.index')->with('success', 'Invoice deleted successfully.');
    }

    public function download(Invoice $invoice)
    {
        $pdf = Pdf::loadView('invoices.pdf', compact('invoice'));
        return $pdf->download('invoice-' . $invoice->invoice_number . '.pdf');
    }
}
