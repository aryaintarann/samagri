@extends('emails.layout')

@section('content')
    <span class="sub-heading" style="color: #059669;">Payment Confirmed</span>
    <h1>Payment Received</h1>

    <p>Thank you! We have successfully received your payment for the following invoice:</p>

    <div class="box box-success" style="text-align: center;">
        <span class="amount-label">AMOUNT PAID</span>
        <span class="amount-value">Rp {{ number_format($invoice->amount, 0, ',', '.') }}</span>
    </div>

    <table class="info-table">
        <tr>
            <td class="label-col">Invoice Number</td>
            <td class="value-col">{{ $invoice->invoice_number }}</td>
        </tr>
        <tr>
            <td class="label-col">Project</td>
            <td class="value-col">{{ $invoice->project->name }}</td>
        </tr>
        <tr>
            <td class="label-col">Payment Date</td>
            <td class="value-col">{{ now()->format('M d, Y') }}</td>
        </tr>
    </table>

    <div class="btn-container">
        <a href="{{ route('invoices.download', $invoice->id) }}" class="btn">Download Receipt</a>
    </div>
@endsection