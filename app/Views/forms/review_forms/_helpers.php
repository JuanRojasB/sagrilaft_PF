<?php
/**
 * Helpers para vistas de revisión de formularios.
 * $form debe estar disponible en el scope que incluye este archivo.
 */

// Shorthand para mostrar un valor del formulario
if (!function_exists('rv')) {
    function rv(array $form, string $key, string $empty = '—'): string {
        $v = $form[$key] ?? null;
        if ($v === null || $v === '' || $v === '0') return "<em style='color:#475569;'>$empty</em>";
        return nl2br(htmlspecialchars((string)$v));
    }
}

// Valor monetario
if (!function_exists('rv_money')) {
    function rv_money(array $form, string $key): string {
        $v = $form[$key] ?? null;
        if ($v === null || $v === '') return "<em style='color:#475569;'>—</em>";
        return '$ ' . number_format((float)$v, 2, '.', ',');
    }
}

// Valor sí/no
if (!function_exists('rv_bool')) {
    function rv_bool(array $form, string $key): string {
        $v = strtolower((string)($form[$key] ?? ''));
        if ($v === 'si' || $v === 'sí' || $v === '1' || $v === 'yes') {
            return "<span style='color:#86efac;font-weight:700;'>SÍ</span>";
        }
        if ($v === 'no' || $v === '0') {
            return "<span style='color:#fca5a5;font-weight:700;'>NO</span>";
        }
        return "<em style='color:#475569;'>—</em>";
    }
}

// Fecha formateada
if (!function_exists('rv_date')) {
    function rv_date(array $form, string $key): string {
        $v = $form[$key] ?? null;
        if (!$v || !strtotime($v)) return "<em style='color:#475569;'>—</em>";
        return date('d/m/Y', strtotime($v));
    }
}

// Formatear tipo de documento
if (!function_exists('format_document_type')) {
    function format_document_type(?string $type): string {
        if (!$type) return '';
        $map = [
            'cedula' => 'Cédula de Ciudadanía',
            'cc' => 'Cédula de Ciudadanía',
            'ce' => 'Cédula de Extranjería',
            'pasaporte' => 'Pasaporte',
            'nit' => 'NIT',
            'ti' => 'Tarjeta de Identidad',
        ];
        $lower = strtolower(trim($type));
        return $map[$lower] ?? ucfirst($type);
    }
}

// Renderizar sección de observaciones si tiene contenido
if (!function_exists('rv_observaciones')) {
    function rv_observaciones(array $form): void {
        $v = trim((string)($form['observaciones'] ?? ''));
        if ($v === '') return;
        echo '<div class="form-section">';
        echo '<div class="section-title">OBSERVACIONES</div>';
        echo '<div class="section-content">';
        echo '<div class="fr cfull">';
        echo '<div class="fv" style="border-right:none; padding:12px; align-items:stretch; white-space:pre-wrap;">';
        echo nl2br(htmlspecialchars($v));
        echo '</div></div></div></div>';
    }
}
