<!DOCTYPE html>
<html>

<head>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            background-color: #f3f4f6;
            padding: 20px;
            color: #374151;
        }

        .container {
            background-color: #ffffff;
            max-width: 600px;
            margin: 0 auto;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        .header {
            border-bottom: 2px solid #f9fafb;
            padding-bottom: 20px;
            margin-bottom: 20px;
            text-align: center;
        }

        .logo {
            font-size: 24px;
            font-weight: bold;
            color: #4338ca;
        }

        .content {
            line-height: 1.6;
        }

        .success-icon {
            text-align: center;
            margin: 20px 0;
        }

        .success-icon span {
            display: inline-block;
            width: 60px;
            height: 60px;
            line-height: 60px;
            border-radius: 50%;
            background-color: #d1fae5;
            color: #059669;
            font-size: 30px;
        }

        .invoice-card {
            background-color: #f9fafb;
            padding: 20px;
            border-radius: 6px;
            margin: 20px 0;
            border: 1px solid #e5e7eb;
        }

        .label {
            font-weight: 600;
            color: #6b7280;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .value {
            color: #111827;
            font-weight: 500;
            font-size: 16px;
            margin-bottom: 12px;
        }

        .amount {
            color: #059669;
            font-weight: bold;
            font-size: 20px;
        }

        .button {
            display: inline-block;
            background-color: #4f46e5;
            color: #ffffff;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 600;
            margin-top: 20px;
        }

        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 12px;
            color: #9ca3af;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <div class="logo">Samagri</div>
        </div>
        <div class="content">
            <div class="success-icon">
                <span>&#10003;</span>
            </div>
            <h2 style="text-align: center;">Payment Received</h2>
            <p>Thank you! We have received your payment for the invoice below:</p>

            <div class="invoice-card">
                <div class="label">Invoice Number</div>
                <div class="value">{{ $invoice->invoice_number }}</div>

                <div class="label">Project</div>
                <div class="value">{{ $invoice->project->name }}</div>

                <div class="label">Amount Paid</div>
                <div class="value amount">Rp {{ number_format($invoice->amount, 0, ',', '.') }}</div>
            </div>

            <p style="text-align: center;">
                <a href="{{ route('invoices.download', $invoice->id) }}" class="button">Download Receipt</a>
            </p>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} Samagri. All rights reserved.
        </div>
    </div>
</body>

</html>