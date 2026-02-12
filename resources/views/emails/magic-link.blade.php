<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }

        .container {
            background: #ffffff;
            border-radius: 8px;
            padding: 30px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
        }

        h1 {
            color: #2563eb;
            margin: 0 0 10px 0;
            font-size: 24px;
        }

        .content {
            margin: 20px 0;
        }

        .button {
            display: inline-block;
            background: #2563eb;
            color: #ffffff;
            padding: 14px 28px;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 600;
            margin: 20px 0;
        }

        .button:hover {
            background: #1d4ed8;
        }

        .button-container {
            text-align: center;
        }

        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            font-size: 14px;
            color: #6b7280;
        }

        .warning {
            background: #fef3c7;
            border-left: 4px solid #f59e0b;
            padding: 12px;
            margin: 20px 0;
            border-radius: 4px;
        }

        .expires {
            color: #dc2626;
            font-weight: 600;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>🔐 Login ke Invoice Digital Services</h1>
            <p>Klik tombol di bawah untuk masuk ke sistem</p>
        </div>

        <div class="content">
            <p>Halo,</p>
            <p>Anda menerima email ini karena ada permintaan Login ke Invoice Digital Services.</p>

            <div class="button-container">
                <a href="{{ $loginToken->getDeepLink() }}" class="button">
                    Masuk ke Aplikasi Native
                </a>
            </div>

            <p style="text-align: center; color: #6b7280; font-size: 14px; margin: 10px 0;">atau</p>

            <div class="button-container">
                <a href="{{ $loginToken->getUrl() }}" class="button" style="background: #64748b;">
                    Buka di Browser
                </a>
            </div>

            <div class="warning">
                <p style="margin: 0;">
                    <strong>⚠️ Penting:</strong> Link ini akan <span class="expires">kedaluwarsa dalam 1 jam</span>.
                </p>
            </div>

            <p>Jika Anda tidak meminta login, abaikan email ini. Link akan otomatis tidak berlaku setelah kedaluwarsa.
            </p>
        </div>

        <div class="footer">
            <p><strong>Keamanan:</strong></p>
            <ul style="margin: 5px 0;">
                <li>Link ini hanya dapat digunakan satu kali</li>
                <li>Jangan bagikan link ini kepada siapapun</li>
                <li>Jika ada yang mencurigakan, hubungi administrator</li>
            </ul>
            <p style="margin-top: 20px; text-align: center;">© {{ date('Y') }} Invoice Digital Services System</p>
        </div>
    </div>
</body>

</html>
