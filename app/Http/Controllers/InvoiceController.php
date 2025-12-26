<?php

namespace App\Http\Controllers;

use App\Http\Requests\Invoices\StoreInvoiceRequest;
use App\Http\Requests\Invoices\UpdateInvoiceRequest;
use App\Models\Invoice;
use App\Models\Project;
use App\Services\InvoiceService;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    protected InvoiceService $invoiceService;

    public function __construct(InvoiceService $invoiceService)
    {
        $this->invoiceService = $invoiceService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $invoices = Invoice::with('project.client')->latest()->get();
        $projects = Project::all(); // Fetch all projects to ensure edit works for inactive ones
        return view('invoices.index', compact('invoices', 'projects'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return redirect()->route('invoices.index');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreInvoiceRequest $request)
    {
        $invoice = $this->invoiceService->createInvoice($request->validated());

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
    public function update(UpdateInvoiceRequest $request, Invoice $invoice)
    {
        $updatedInvoice = $this->invoiceService->updateInvoice($invoice, $request->validated());

        if ($request->ajax()) {
            return response()->json(['message' => 'Invoice updated successfully', 'invoice' => $updatedInvoice]);
        }

        return redirect()->route('invoices.index')->with('success', 'Invoice updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Invoice $invoice)
    {
        $this->invoiceService->deleteInvoice($invoice);

        if (request()->ajax()) {
            return response()->json(['message' => 'Invoice deleted successfully']);
        }

        return redirect()->route('invoices.index')->with('success', 'Invoice deleted successfully.');
    }

    public function download(Invoice $invoice)
    {
        $pdf = $this->invoiceService->generatePdf($invoice);
        return $pdf->stream('invoice-' . $invoice->invoice_number . '.pdf');
    }
}
