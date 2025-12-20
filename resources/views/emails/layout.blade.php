<!DOCTYPE html>
<html lang="en" xmlns="http://www.w3.org/1999/xhtml" xmlns:o="urn:schemas-microsoft-com:office:office">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="x-apple-disable-message-reformatting">
    <title>Samagri Notification</title>
    <!--[if mso]>
    <noscript>
        <xml>
            <o:OfficeDocumentSettings>
                <o:PixelsPerInch>96</o:PixelsPerInch>
            </o:OfficeDocumentSettings>
        </xml>
    </noscript>
    <![endif]-->
    <style>
        table,
        td,
        div,
        h1,
        p {
            font-family: 'Segoe UI', 'Helvetica Neue', Arial, sans-serif;
        }

        body {
            margin: 0;
            padding: 0;
            background-color: #f3f4f6;
        }

        .wrapper {
            width: 100%;
            table-layout: fixed;
            background-color: #f3f4f6;
            padding-top: 40px;
            padding-bottom: 60px;
        }

        .container {
            background-color: #ffffff;
            margin: 0 auto;
            max-width: 600px;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            font-size: 16px;
            line-height: 1.6;
            color: #374151;
        }

        .header-bar {
            height: 6px;
            width: 100%;
            background: linear-gradient(90deg, #1e3a8a 0%, #3b82f6 100%);
        }

        .header-content {
            padding: 32px 40px;
            text-align: center;
            border-bottom: 1px solid #f3f4f6;
        }

        .logo {
            font-size: 26px;
            font-weight: 800;
            color: #1e3a8a;
            display: inline-block;
            text-decoration: none;
            letter-spacing: -0.5px;
        }

        .body-content {
            padding: 40px 40px 20px 40px;
        }

        /* Typography */
        h1 {
            margin-top: 0;
            margin-bottom: 16px;
            font-size: 24px;
            font-weight: 700;
            color: #111827;
        }

        h2 {
            margin-top: 0;
            margin-bottom: 12px;
            font-size: 20px;
            font-weight: 600;
            color: #1f2937;
        }

        p {
            margin-top: 0;
            margin-bottom: 24px;
        }

        /* Components */
        .sub-heading {
            font-size: 13px;
            font-weight: 700;
            color: #1e3a8a;
            text-transform: uppercase;
            letter-spacing: 1.2px;
            margin-bottom: 12px;
            display: block;
        }

        /* Table-based Grid */
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 24px;
        }

        .info-table td {
            padding: 12px 0;
            border-bottom: 1px solid #f3f4f6;
            vertical-align: top;
        }

        .info-table tr:last-child td {
            border-bottom: none;
        }

        .label-col {
            width: 35%;
            color: #6b7280;
            font-weight: 600;
            font-size: 14px;
        }

        .value-col {
            width: 65%;
            color: #111827;
            font-weight: 600;
            text-align: right;
        }

        .box {
            background-color: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 24px;
        }

        .box-success {
            background-color: #ecfdf5;
            border-color: #a7f3d0;
        }

        .box-success .amount-label {
            color: #047857;
            font-size: 14px;
            font-weight: 600;
            display: block;
        }

        .box-success .amount-value {
            color: #065f46;
            font-size: 28px;
            font-weight: 800;
            display: block;
            margin-top: 4px;
        }

        .btn-container {
            text-align: center;
            margin-top: 32px;
            margin-bottom: 10px;
        }

        .btn {
            display: inline-block;
            background-color: #1e3a8a;
            color: #ffffff !important;
            text-decoration: none;
            padding: 14px 32px;
            border-radius: 6px;
            font-weight: 600;
            font-size: 15px;
            box-shadow: 0 4px 6px -1px rgba(30, 58, 138, 0.2);
            transition: all 0.2s;
        }

        .btn:hover {
            background-color: #1e40af;
            box-shadow: 0 6px 8px -1px rgba(30, 58, 138, 0.3);
            color: #ffffff !important;
        }

        .footer {
            padding: 32px 40px;
            background-color: #f9fafb;
            text-align: center;
            border-top: 1px solid #f3f4f6;
        }

        .copyright {
            color: #9ca3af;
            font-size: 13px;
            margin-bottom: 12px;
        }

        .footer-links a {
            color: #6b7280;
            text-decoration: none;
            font-size: 13px;
            margin: 0 8px;
        }
    </style>
</head>

<body>
    <div class="wrapper">
        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0">
            <tr>
                <td align="center">
                    <div class="container">
                        <!-- Colored Bar -->
                        <div class="header-bar"></div>

                        <!-- Header / Logo -->
                        <div class="header-content">
                            <a href="{{ url('/') }}" class="logo">Samagri</a>
                        </div>

                        <!-- Main Content -->
                        <div class="body-content">
                            @yield('content')
                        </div>

                        <!-- Footer -->
                        <div class="footer">
                            <p class="copyright">&copy; {{ date('Y') }} Samagri Group. All rights reserved.</p>
                            <div class="footer-links">
                                <a href="#">Privacy Policy</a> • <a href="#">Support</a>
                            </div>
                        </div>
                    </div>
                </td>
            </tr>
        </table>
    </div>
</body>

</html>