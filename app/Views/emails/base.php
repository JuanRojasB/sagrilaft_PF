<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'SAGRILAFT' ?></title>
    <style>
        /* Emails usan colores inline para compatibilidad con clientes de correo */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            line-height: 1.6;
            color: #e5e7eb;
            background-color: #020617;
            padding: 20px;
        }
        .email-wrapper {
            max-width: 600px;
            margin: 0 auto;
            background-color: rgba(15, 23, 42, 0.9);
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 20px 25px -5px rgba(15, 23, 42, 0.7);
            border: 1px solid rgba(30, 64, 175, 0.7);
        }
        .email-header {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            padding: 30px 20px;
            text-align: center;
            border-bottom: 2px solid rgba(56, 189, 248, 0.6);
        }
        .email-header h1 {
            color: #e5e7eb;
            font-size: 24px;
            font-weight: 600;
            margin: 0;
        }
        .email-header p {
            color: #cbd5e1;
            font-size: 14px;
            margin: 8px 0 0;
        }
        .email-body {
            padding: 30px 20px;
            background: #0f172a;
        }
        .status-badge {
            display: inline-block;
            padding: 12px 24px;
            border-radius: 999px;
            font-weight: 600;
            font-size: 16px;
            margin: 20px 0;
            text-align: center;
        }
        .status-approved {
            background-color: rgba(34, 197, 94, 0.2);
            color: #86efac;
            border: 2px solid rgba(34, 197, 94, 0.5);
        }
        .status-rejected {
            background-color: rgba(239, 68, 68, 0.2);
            color: #fca5a5;
            border: 2px solid rgba(239, 68, 68, 0.5);
        }
        .status-pending {
            background-color: rgba(251, 191, 36, 0.1);
            color: #fbbf24;
            border: 2px solid rgba(251, 191, 36, 0.5);
        }
        .status-info {
            background-color: rgba(59, 130, 246, 0.2);
            color: #93c5fd;
            border: 2px solid rgba(59, 130, 246, 0.5);
        }
        .info-card {
            background-color: #1e293b;
            border: 1px solid rgba(148, 163, 184, 0.3);
            border-radius: 8px;
            padding: 16px;
            margin: 16px 0;
        }
        .info-row {
            display: flex;
            padding: 8px 0;
            border-bottom: 1px solid rgba(148, 163, 184, 0.3);
        }
        .info-row:last-child {
            border-bottom: none;
        }
        .info-label {
            font-weight: 600;
            color: #cbd5e1;
            min-width: 140px;
            font-size: 14px;
        }
        .info-value {
            color: #e5e7eb;
            font-size: 14px;
            flex: 1;
        }
        .observations-box {
            background-color: rgba(251, 191, 36, 0.1);
            border-left: 4px solid #f59e0b;
            padding: 16px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .observations-box strong {
            color: #fbbf24;
            display: block;
            margin-bottom: 8px;
            font-size: 14px;
        }
        .observations-box p {
            color: #fbbf24;
            font-size: 14px;
            margin: 0;
        }
        .button {
            display: inline-block;
            padding: 12px 24px;
            background: linear-gradient(135deg, #1d4ed8, #38bdf8);
            color: #e5e7eb !important;
            text-decoration: none;
            border-radius: 999px;
            font-weight: 600;
            font-size: 14px;
            margin: 20px 0;
            text-align: center;
            box-shadow: 0 10px 15px -3px rgba(30, 64, 175, 0.8);
        }
        .button:hover {
            filter: brightness(1.05);
        }
        .email-footer {
            background-color: #0f172a;
            padding: 20px;
            text-align: center;
            border-top: 1px solid rgba(148, 163, 184, 0.3);
        }
        .email-footer p {
            color: #9ca3af;
            font-size: 12px;
            margin: 4px 0;
        }
        .divider {
            height: 1px;
            background-color: rgba(148, 163, 184, 0.3);
            margin: 20px 0;
        }
        @media only screen and (max-width: 600px) {
            .email-wrapper {
                border-radius: 0;
            }
            .email-body {
                padding: 20px 15px;
            }
            .info-row {
                flex-direction: column;
            }
            .info-label {
                min-width: auto;
                margin-bottom: 4px;
            }
        }
    </style>
</head>
<body>
    <div class="email-wrapper">
        <div class="email-header">
            <h1>SAGRILAFT</h1>
        </div>
        
        <div class="email-body">
            <?= $content ?? '' ?>
        </div>
        
        <div class="email-footer">
            <p><strong>SAGRILAFT</strong></p>
            <p>&copy; <?= date('Y') ?> Todos los derechos reservados</p>
            <p style="margin-top: 12px; color: #94a3b8;">Este es un mensaje automático, por favor no responder a este correo.</p>
        </div>
    </div>
</body>
</html>
