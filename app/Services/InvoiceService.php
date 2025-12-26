<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\Project;
use App\Traits\LogsActivity;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

class InvoiceService
{
    use LogsActivity;

    public function createInvoice(array $data): Invoice
    {
        return DB::transaction(function () use ($data) {
            $project = Project::with('client')->findOrFail($data['project_id']);

            $invoice = Invoice::create([
                'project_id' => $data['project_id'],
                'amount' => $data['amount'],
                'status' => $data['status'],
                'due_date' => $data['due_date'] ?? null,
                'invoice_number' => 'INV-' . time(), // Simple logic, can be improved
            ]);

            $this->logActivity('Created Invoice', 'Created invoice ' . $invoice->invoice_number . ' for project: ' . $project->name);

            // Send Email Notification to Client if available
            if ($project->client && $project->client->email) {
                // Ensure Notification class exists or handle error
                // $project->client->notify(new \App\Notifications\InvoiceCreated($invoice));
            }

            return $invoice;
        });
    }

    public function updateInvoice(Invoice $invoice, array $data): Invoice
    {
        return DB::transaction(function () use ($invoice, $data) {
            $invoice->update([
                'project_id' => $data['project_id'],
                'amount' => $data['amount'],
                'status' => $data['status'],
                'due_date' => $data['due_date'] ?? null,
            ]);

            $this->logActivity('Updated Invoice', 'Updated invoice ' . $invoice->invoice_number . ' status to: ' . $invoice->status);

            // Send Email Notification if Paid
            // Enum comparison is handled via casting in Model ideally, but here request gives Enum instance or value
            // If passed from Request with Enum rule, it might be string or Enum.
            // Let's assume data['status'] is the value or Enum backing value.

            $statusValue = $data['status'] instanceof \BackedEnum ? $data['status']->value : $data['status'];

            if ($statusValue === 'Paid' && $invoice->project->client && $invoice->project->client->email) {
                // $invoice->project->client->notify(new \App\Notifications\InvoicePaid($invoice));
            }

            return $invoice;
        });
    }

    public function deleteInvoice(Invoice $invoice): void
    {
        DB::transaction(function () use ($invoice) {
            $number = $invoice->invoice_number;
            $invoice->delete();
            $this->logActivity('Deleted Invoice', 'Deleted invoice: ' . $number);
        });
    }

    public function generatePdf(Invoice $invoice)
    {
        // This logic remains here or can be in a dedicated PDF Service if complex
        // For now, Service handling it is better than Controller
        $pdf = Pdf::loadView('invoices.pdf', compact('invoice'));
        return $pdf;
    }
}
