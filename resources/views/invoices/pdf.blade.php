<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Invoice #{{ $invoice->invoice_number }}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
            font-size: 14px;
            line-height: 20px;
            color: #555;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 30px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        /* Header */
        .header-table td {
            vertical-align: middle;
        }

        .logo {
            height: 60px;
            width: auto;
        }

        .invoice-title {
            text-align: right;
            color: #1e3a8a;
            /* Navy Blue */
            font-size: 32px;
            font-weight: bold;
            text-transform: uppercase;
            margin: 0;
        }

        .invoice-meta {
            text-align: right;
            color: #777;
            font-size: 13px;
            margin-top: 5px;
        }

        /* Separator */
        .separator {
            border-bottom: 2px solid #1e3a8a;
            margin: 25px 0;
            height: 0;
        }

        /* Info Section */
        .info-table td {
            vertical-align: top;
            width: 50%;
        }

        .label-title {
            color: #1e3a8a;
            font-weight: bold;
            font-size: 14px;
            text-transform: uppercase;
            margin-bottom: 5px;
        }

        .address-block {
            line-height: 1.5;
        }

        /* Items Section */
        .items-header {
            background-color: #1e3a8a;
            color: white;
            font-weight: bold;
            text-transform: uppercase;
        }

        .items-header td {
            padding: 10px 15px;
        }

        .item-row td {
            padding: 10px 15px;
            border-bottom: 1px solid #eee;
        }

        .total-row td {
            padding: 10px 15px;
            border-top: 2px solid #1e3a8a;
            font-weight: bold;
            background-color: #f9fafb;
            color: #333;
        }

        .text-right {
            text-align: right;
        }

        /* Payment Details */
        .payment-box {
            background-color: #f3f4f6;
            border-left: 4px solid #1e3a8a;
            padding: 15px;
            margin-top: 30px;
        }

        .payment-title {
            color: #1e3a8a;
            font-weight: bold;
            font-size: 14px;
            margin-bottom: 5px;
        }

        /* Status Badge */
        .status-badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
            margin-top: 5px;
        }

        .status-paid {
            background-color: #d1fae5;
            color: #065f46;
            border: 1px solid #a7f3d0;
        }

        .status-pending {
            background-color: #fee2e2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }

        /* Footer */
        .footer {
            margin-top: 40px;
            border-top: 1px solid #eee;
            padding-top: 20px;
            text-align: center;
            font-size: 12px;
            color: #aaa;
        }
    </style>
</head>

<body>
    <div class="container">
        <!-- Header -->
        <table class="header-table">
            <tr>
                <td style="width: 60%; text-align: left;">
                    <img src="{{ public_path('logo.png') }}" class="logo" alt="Samagri Logo" height="60">
                </td>
                <td style="width: 40%; text-align: right;">
                    <div class="invoice-title">INVOICE</div>
                    <div class="invoice-meta">
                        <strong>#{{ $invoice->invoice_number }}</strong><br>
                        Date: {{ $invoice->created_at->format('M d, Y') }}<br>
                        Due: {{ $invoice->due_date ? $invoice->due_date->format('M d, Y') : 'N/A' }}<br>
                        <span class="status-badge {{ $invoice->status == 'Paid' ? 'status-paid' : 'status-pending' }}">
                            {{ $invoice->status }}
                        </span>
                    </div>
                </td>
            </tr>
        </table>

        <!-- Wrapper for visual separator -->
        <div class="separator"></div>

        <!-- Info Section (Bill To / Pay To) -->
        <table class="info-table">
            <tr>
                <!-- Left: Bill To -->
                <td style="padding-right: 20px;">
                    <div class="label-title">Bill To:</div>
                    <div class="address-block">
                        <strong>{{ $invoice->project->client->name }}</strong><br>
                        @if($invoice->project->client->company)
                            {{ $invoice->project->client->company }}<br>
                        @endif
                        {{ $invoice->project->client->email }}<br>
                        @if($invoice->project->client->phone)
                            {{ $invoice->project->client->phone }}
                        @endif
                    </div>
                </td>
                <!-- Right: Pay To -->
                <td class="text-right" style="padding-left: 20px;">
                    <div class="label-title">Pay To:</div>
                    <div class="address-block">
                        <strong>SamagriTech</strong><br>
                        Jl. Gunung Andakasa, Gg. Bougenville No. D-1<br>
                        Bali, Indonesia<br>
                        finance@samagritech.com
                    </div>
                </td>
            </tr>
        </table>

        <!-- Spacer -->
        <div style="height: 30px;"></div>

        <!-- Items Table -->
        <table class="items-table">
            <tr class="items-header">
                <td style="width: 70%;">Description</td>
                <td style="width: 30%; text-align: right;">Amount</td>
            </tr>
            <tr class="item-row">
                <td>
                    <strong>Project: {{ $invoice->project->name }}</strong><br>
                    <span style="color: #777; font-size: 12px;">Service provided for implementation and
                        development.</span>
                </td>
                <td class="text-right">
                    Rp {{ number_format($invoice->amount, 0, ',', '.') }}
                </td>
            </tr>
            <!-- Optional: Spacer Row for minimum height if needed -->
            <!-- Total -->
            <tr class="total-row">
                <td class="text-right" style="border-right: none;">Total Due</td>
                <td class="text-right">Rp {{ number_format($invoice->amount, 0, ',', '.') }}</td>
            </tr>
        </table>

        <!-- Payment Details -->
        <div class="payment-box">
            <div class="payment-title">Payment Instructions</div>
            <div style="font-size: 13px;">
                Please make payment via Bank Transfer to:<br>
                <div style="margin-top: 8px;">
                    <strong>Bank BCA</strong> &nbsp;|&nbsp;
                    Account: <strong>6115843102</strong> &nbsp;|&nbsp;
                    Name: <strong>Arya Ngurah Intaran</strong>
                </div>
                <div style="margin-top: 8px; font-size: 11px; color: #777;">
                    *Please include Invoice #{{ $invoice->invoice_number }} in your transfer news.
                </div>
                <div style="margin-top: 5px; font-size: 11px; color: #777;">
                    *Payments are currently routed to a personal account on behalf of the SamagriTech owner. Thank you
                    for your understanding.
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            Thank you for your business!<br>
            Any questions? Contact us at finance@samagritech.com
        </div>
    </div>
</body>

</html>