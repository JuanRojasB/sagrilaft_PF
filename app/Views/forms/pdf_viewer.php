<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'PDF - SAGRILAFT') ?></title>
    <link rel="icon" type="image/png" href="/gestion-sagrilaft/public/assets/img/orb-logo.png">
    <style>
        html, body { margin: 0; padding: 0; height: 100%; background: #ffffff; }
        .pdf-frame { width: 100%; height: 100%; border: 0; display: block; }
    </style>
</head>
<body>
    <iframe class="pdf-frame" src="<?= htmlspecialchars($pdf_url ?? '') ?>" title="Visor PDF SAGRILAFT"></iframe>
</body>
</html>
