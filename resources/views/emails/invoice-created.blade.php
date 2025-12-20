@extends('emails.layout')

@section('content')
    <span class="sub-heading">Billing Update</span>
    <h1>New Invoice Available</h1>

    <p>Hello, a new invoice has been generated for your project. Please find the details below:</p>

    <div class="box">
        <table class="info-table">
            <tr>
                <td class="label-col">Invoice #</td>
                <td class="value-col">{{ $invoice->invoice_number }}</td>
            </tr>
            <tr>
                <td class="label-col">Project</td>
                <td class="value-col">{{ $invoice->project->name }}</td>
            </tr>
            <tr>
                <td class="label-col">Due Date</td>
                <td class="value-col">{{ $invoice->due_date ? $invoice->due_date->format('M d, Y') : 'Immediate' }}</td>
            </tr>
            <tr style="border-top: 1px dashed #e5e7eb;">
                <td class="label-col" style="padding-top: 15px; color: #111827;">Amount Due</td>
                <td class="value-col" style="padding-top: 15px; font-size: 18px; color: #1e3a8a;">Rp
                    {{ number_format($invoice->amount, 0, ',', '.') }}</td>
            </tr>
        </table>
    </div>

    <div class="btn-container">
        <a href="{{ route('invoices.download', $invoice->id) }}" class="btn">Download Invoice PDF</a>
    </div>
@endsection