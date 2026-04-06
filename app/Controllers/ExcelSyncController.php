<?php

namespace App\Controllers;

use App\Core\Controller;

class ExcelSyncController extends Controller
{
    // ── Columnas por sección ──────────────────────────────────────────────────
    private const COLS_COMUNES = [
        ['id',               'ID',               40,  'N'],
        ['form_type',        'Tipo Formulario',  110, 'T'],
        ['title',            'Titulo',           140, 'T'],
        ['user_name',        'Usuario',          130, 'T'],
        ['user_email',       'Email Usuario',    170, 'T'],
        ['user_phone',       'Tel. Usuario',     90,  'T'],
    ];

    private const COLS_DATOS_GENERALES = [
        ['company_name',  'Empresa / Nombre',  170, 'T'],
        ['nit',           'NIT / Documento',   100, 'T'],
        ['address',       'Direccion',         180, 'W'],
        ['ciudad',        'Ciudad',            100, 'T'],
        ['barrio',        'Barrio',            100, 'T'],
        ['localidad',     'Localidad',         100, 'T'],
        ['telefono_fijo', 'Tel. Fijo',          90, 'T'],
        ['celular',       'Celular',            90, 'T'],
        ['phone',         'Telefono',           90, 'T'],
        ['empresa_email', 'Email Empresa',     170, 'T'],
        ['fax',           'Fax',                70, 'T'],
    ];

    private const COLS_ACTIVIDAD = [
        ['activity',      'Actividad Economica', 200, 'W'],
        ['codigo_ciiu',   'Codigo CIIU',          80, 'T'],
        ['objeto_social', 'Objeto Social',        200, 'W'],
    ];

    private const COLS_FINANCIERA = [
        ['activos',                'Activos $',            110, 'N'],
        ['pasivos',                'Pasivos $',            110, 'N'],
        ['patrimonio',             'Patrimonio $',         110, 'N'],
        ['ingresos',               'Ingresos $',           110, 'N'],
        ['gastos',                 'Gastos $',             110, 'N'],
        ['otros_ingresos',         'Otros Ingresos $',     110, 'N'],
        ['detalle_otros_ingresos', 'Detalle Otros Ing.',   200, 'W'],
    ];

    private const COLS_TRIBUTARIA = [
        ['tipo_contribuyente', 'Tipo Contribuyente', 130, 'T'],
        ['regimen_tributario', 'Regimen Tributario', 130, 'T'],
    ];

    private const COLS_REPRESENTANTE = [
        ['representante_nombre',    'Representante Legal', 160, 'T'],
        ['representante_documento', 'Doc. Representante',  110, 'T'],
        ['representante_tipo_doc',  'Tipo Doc. Rep.',      100, 'T'],
        ['representante_profesion', 'Profesion Rep.',      110, 'T'],
        ['representante_nacimiento','Nacimiento Rep.',     100, 'T'],
        ['representante_telefono',  'Tel. Representante',  100, 'T'],
        ['representante_direccion', 'Dir. Representante',  170, 'W'],
    ];

    private const COLS_ACCIONISTAS = [
        ['accionistas', 'Accionistas (>5%)', 250, 'W'],
    ];

    private const COLS_CLIENTE = [
        ['lista_precios',     'Lista Precios',      110, 'T'],
        ['codigo_vendedor',   'Cod. Vendedor',      100, 'T'],
        ['tipo_pago',         'Tipo Pago',           90, 'T'],
        ['cupo_credito',      'Cupo Credito',        110, 'N'],
        ['fecha_nacimiento',  'Fecha Nacimiento',    100, 'T'],
        ['clase_cliente',     'Clase Cliente',       110, 'T'],
        ['descripcion_firma', 'Descripcion Firma',   180, 'W'],
        ['nombre_firmante',   'Nombre Firmante',     150, 'T'],
    ];

    private const COLS_PROVEEDOR = [
        ['tipo_compania',       'Tipo Compania',        110, 'T'],
        ['persona_contacto',    'Persona Contacto',     150, 'T'],
        ['tiene_certificacion', 'Tiene Certificacion',  110, 'T'],
        ['cual_certificacion',  'Cual Certificacion',   170, 'T'],
        ['nombre_firmante',     'Nombre Firmante',      150, 'T'],
    ];

    private const COLS_IMPORTACION = [
        ['pais',                     'Pais',                    100, 'T'],
        ['concepto_importacion',     'Concepto Importacion',    200, 'W'],
        ['declaracion_importacion',  'Declaracion Importacion', 150, 'T'],
        ['certificado_origen',       'Certificado Origen',      150, 'T'],
        ['certificado_transporte',   'Certificado Transporte',  150, 'T'],
        ['certificado_fitosanitario','Cert. Fitosanitario',     150, 'T'],
        ['copia_swift',              'Copia SWIFT',             150, 'T'],
    ];

    private const COLS_FONDOS_PEP = [
        ['origen_fondos',           'Origen de Fondos',          200, 'W'],
        ['es_pep',                  'Es PEP',                     70, 'T'],
        ['cargo_pep',               'Cargo PEP',                 150, 'T'],
        ['fecha_vinculacion_pep',   'Fecha Vinculacion PEP',     110, 'T'],
        ['fecha_desvinculacion_pep','Fecha Desvinculacion PEP',  110, 'T'],
        ['relacion_pep',            'Relacion PEP',              150, 'T'],
        ['identificacion_pep',      'Identificacion PEP',        130, 'T'],
        ['familiares_pep',          'Familiares PEP',            200, 'W'],
        ['tiene_cuentas_exterior',  'Cuentas Exterior',          110, 'T'],
        ['pais_cuentas_exterior',   'Pais Cuentas Ext.',         120, 'T'],
    ];

    private const COLS_AUTORIZACIONES = [
        ['autoriza_centrales_riesgo',  'Autoriza Centrales Riesgo', 130, 'T'],
        ['consulta_ofac',              'Consulta OFAC',             100, 'T'],
        ['consulta_listas_nacionales', 'Listas Nacionales',         130, 'T'],
        ['consulta_onu',               'Consulta ONU',              100, 'T'],
    ];

    private const COLS_INTERNOS = [
        ['director_cartera',           'Director Cartera',       150, 'T'],
        ['gerencia_comercial',         'Gerencia Comercial',     150, 'T'],
        ['oficial_cumplimiento',       'Oficial Cumplimiento',   150, 'T'],
        ['fecha_oficial_cumplimiento', 'Fecha Oficial Cumpl.',   120, 'T'],
    ];

    private const COLS_CONTROL = [
        ['status',               'Status',                  90,  'T'],
        ['approval_status',      'Estado Aprobacion',       120, 'STATUS'],
        ['approval_date',        'Fecha Aprobacion',        120, 'T'],
        ['approved_by',          'Aprobado Por',            150, 'T'],
        ['approval_observations','Observaciones',           250, 'W'],
        ['related_form_id',      'Form. Relacionado (ID)',  110, 'N'],
        ['archivos_adjuntos',    'Archivos Adjuntos',       250, 'W'],
        ['created_at',           'Fecha Creacion',          120, 'T'],
        ['updated_at',           'Ultima Actualizacion',    120, 'T'],
    ];

    // ── Mapeo de hojas por tipo de formulario ─────────────────────────────────
    private function getSheets(): array
    {
        return [
            [
                'name'  => 'Clientes Naturales',
                'types' => ['cliente'],
                'cols'  => array_merge(
                    self::COLS_COMUNES, self::COLS_DATOS_GENERALES,
                    self::COLS_ACTIVIDAD, self::COLS_FINANCIERA,
                    self::COLS_TRIBUTARIA, self::COLS_CLIENTE,
                    self::COLS_AUTORIZACIONES, self::COLS_INTERNOS, self::COLS_CONTROL
                ),
            ],
            [
                'name'  => 'Clientes Juridicos',
                'types' => ['cliente_juridica'],
                'cols'  => array_merge(
                    self::COLS_COMUNES, self::COLS_DATOS_GENERALES,
                    self::COLS_ACTIVIDAD, self::COLS_FINANCIERA,
                    self::COLS_TRIBUTARIA, self::COLS_REPRESENTANTE,
                    self::COLS_ACCIONISTAS, self::COLS_CLIENTE,
                    self::COLS_AUTORIZACIONES, self::COLS_INTERNOS, self::COLS_CONTROL
                ),
            ],
            [
                'name'  => 'Proveedores Naturales',
                'types' => ['proveedor', 'proveedor_juridico'],
                'cols'  => array_merge(
                    self::COLS_COMUNES, self::COLS_DATOS_GENERALES,
                    self::COLS_ACTIVIDAD, self::COLS_FINANCIERA,
                    self::COLS_REPRESENTANTE, self::COLS_PROVEEDOR,
                    self::COLS_FONDOS_PEP, self::COLS_AUTORIZACIONES,
                    self::COLS_INTERNOS, self::COLS_CONTROL
                ),
            ],
            [
                'name'  => 'Proveedores Internacionales',
                'types' => ['proveedor_internacional'],
                'cols'  => array_merge(
                    self::COLS_COMUNES, self::COLS_DATOS_GENERALES,
                    self::COLS_IMPORTACION, self::COLS_ACTIVIDAD,
                    self::COLS_FINANCIERA, self::COLS_REPRESENTANTE,
                    self::COLS_ACCIONISTAS, self::COLS_PROVEEDOR,
                    self::COLS_AUTORIZACIONES, self::COLS_INTERNOS, self::COLS_CONTROL
                ),
            ],
            [
                'name'  => 'Transportistas',
                'types' => ['transportista'],
                'cols'  => array_merge(
                    self::COLS_COMUNES, self::COLS_DATOS_GENERALES,
                    self::COLS_ACTIVIDAD, self::COLS_FINANCIERA,
                    self::COLS_REPRESENTANTE, self::COLS_FONDOS_PEP,
                    self::COLS_AUTORIZACIONES, self::COLS_INTERNOS, self::COLS_CONTROL
                ),
            ],
        ];
    }


    // ── Descarga ──────────────────────────────────────────────────────────────
    public function downloadFiltered(): void
    {
        if (function_exists('opcache_reset')) opcache_reset();

        try {
            $formIds    = $_POST['form_ids']    ?? '';
            $filterInfo = json_decode($_POST['filter_info'] ?? '{}', true) ?: [];

            if (empty($formIds)) die('No se proporcionaron formularios para exportar');

            $ids = array_values(array_filter(array_map('intval', explode(',', $formIds))));
            if (empty($ids)) die('IDs de formularios invalidos');

            $workbook = $this->buildWorkbook($ids, $filterInfo);

            $filename = 'sagrilaft_';
            if (!empty($filterInfo['status']) && $filterInfo['status'] !== 'all') {
                $filename .= $filterInfo['status'] . '_';
            }
            if (!empty($filterInfo['role']) && $filterInfo['role'] !== 'all') {
                $filename .= $filterInfo['role'] . '_';
            }
            $filename .= date('Y-m-d_His') . '.xls';

            header('Content-Type: application/vnd.ms-excel; charset=UTF-8');
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            header('Cache-Control: no-cache, no-store, must-revalidate');
            header('Pragma: no-cache');
            header('Expires: 0');
            echo $workbook;
            exit;

        } catch (\Exception $e) {
            die('Error al generar Excel: ' . $e->getMessage());
        }
    }

    // ── Construcción del workbook ─────────────────────────────────────────────
    private function buildWorkbook(array $ids, array $filterInfo): string
    {
        $db           = new \App\Core\Database();
        $placeholders = implode(',', array_fill(0, count($ids), '?'));

        // Una sola query con todos los campos
        $stmt = $db->getConnection()->prepare("
            SELECT f.*,
                   u.name  AS user_name,
                   u.email AS user_email,
                   u.phone AS user_phone,
                   f.email AS empresa_email
            FROM forms f
            LEFT JOIN users u ON f.user_id = u.id
            WHERE f.id IN ($placeholders)
            ORDER BY f.id DESC
        ");
        $stmt->execute($ids);
        $allRows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        if (empty($allRows)) {
            die('No se encontraron formularios con los IDs proporcionados');
        }

        // Archivos adjuntos en una sola query
        $stmtF = $db->getConnection()->prepare("
            SELECT form_id, filename, ROUND(filesize/1024/1024, 2) AS mb
            FROM form_attachments
            WHERE form_id IN ($placeholders)
            ORDER BY form_id, id
        ");
        $stmtF->execute($ids);
        $attachments = [];
        foreach ($stmtF->fetchAll() as $f) {
            $attachments[$f['form_id']][] = $f['filename'] . ' (' . $f['mb'] . ' MB)';
        }

        // Enriquecer filas
        foreach ($allRows as &$row) {
            $row['archivos_adjuntos'] = implode('; ', $attachments[$row['id']] ?? []);

            if (!empty($row['accionistas'])) {
                $arr = json_decode($row['accionistas'], true);
                if (is_array($arr)) {
                    $parts = [];
                    foreach ($arr as $a) {
                        $line = trim(($a['nombre'] ?? '') . ' ' . ($a['documento'] ?? ''));
                        if (!empty($a['porcentaje'])) $line .= ' ' . $a['porcentaje'] . '%';
                        if (!empty($a['nacionalidad'])) $line .= ' (' . $a['nacionalidad'] . ')';
                        $parts[] = $line;
                    }
                    $row['accionistas'] = implode(' | ', $parts);
                }
            }
        }
        unset($row);

        // Agrupar por form_type
        $byType = [];
        foreach ($allRows as $row) {
            $type = $row['form_type'] ?? 'otro';
            $byType[$type][] = $row;
        }

        // Filtro de rol activo → limitar hojas
        $roleToTypes = [
            'cliente'      => ['cliente', 'cliente_juridica'],
            'proveedor'    => ['proveedor', 'proveedor_juridico', 'proveedor_internacional'],
            'transportista'=> ['transportista'],
        ];
        $activeRole   = $filterInfo['role'] ?? 'all';
        $allowedTypes = ($activeRole !== 'all' && isset($roleToTypes[$activeRole]))
            ? $roleToTypes[$activeRole]
            : null;

        // Construir XML
        $xml  = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<?mso-application progid="Excel.Sheet"?>' . "\n";
        $xml .= '<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet"'
              . ' xmlns:o="urn:schemas-microsoft-com:office:office"'
              . ' xmlns:x="urn:schemas-microsoft-com:office:excel"'
              . ' xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet"'
              . ' xmlns:html="http://www.w3.org/TR/REC-html40">' . "\n";
        $xml .= $this->buildStyles();

        $sheetsGenerated = 0;

        foreach ($this->getSheets() as $sheet) {
            // Si hay filtro de rol, saltar hojas que no apliquen
            if ($allowedTypes !== null) {
                if (empty(array_intersect($sheet['types'], $allowedTypes))) continue;
            }

            // Recoger filas de los tipos de esta hoja
            $rows = [];
            foreach ($sheet['types'] as $t) {
                $rows = array_merge($rows, $byType[$t] ?? []);
            }

            // Saltar hojas vacías
            if (empty($rows)) continue;

            $xml .= $this->buildSheet($sheet['name'], $sheet['cols'], $rows);
            $sheetsGenerated++;
        }

        // Si no se generó ninguna hoja (caso raro), crear una con todos los datos
        if ($sheetsGenerated === 0) {
            $allCols = array_merge(
                self::COLS_COMUNES, self::COLS_DATOS_GENERALES,
                self::COLS_ACTIVIDAD, self::COLS_FINANCIERA,
                self::COLS_REPRESENTANTE, self::COLS_CLIENTE,
                self::COLS_PROVEEDOR, self::COLS_AUTORIZACIONES,
                self::COLS_INTERNOS, self::COLS_CONTROL
            );
            $xml .= $this->buildSheet('Formularios', $allCols, $allRows);
        }

        $xml .= '</Workbook>';
        return $xml;
    }


    // ── Construcción de una hoja ──────────────────────────────────────────────
    private function buildSheet(string $name, array $cols, array $rows): string
    {
        $safeName = htmlspecialchars(substr($name, 0, 31), ENT_XML1, 'UTF-8');
        $xml = '<Worksheet ss:Name="' . $safeName . '"><Table>';

        foreach ($cols as [, , $w]) {
            $xml .= '<Column ss:Width="' . $w . '"/>';
        }

        // Cabecera
        $xml .= '<Row ss:AutoFitHeight="0" ss:Height="30">';
        foreach ($cols as [, $label]) {
            $xml .= '<Cell ss:StyleID="H"><Data ss:Type="String">'
                  . htmlspecialchars($label, ENT_XML1, 'UTF-8')
                  . '</Data></Cell>';
        }
        $xml .= '</Row>';

        // Helpers
        $esc = function (string $v): string {
            $v = htmlspecialchars($v, ENT_XML1 | ENT_QUOTES, 'UTF-8');
            return preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', '', $v);
        };
        $clean = function (?string $v) use ($esc): string {
            $v = strip_tags($v ?? '');
            $v = str_replace(["\r\n", "\r", "\n", "\t"], ' ', $v);
            return $esc(trim(preg_replace('/\s+/', ' ', $v)));
        };
        $fmtDate = fn($d) => (!empty($d) && $d !== '0000-00-00') ? date('d/m/Y', strtotime($d)) : '';
        $fmtDT   = fn($d) => (!empty($d) && !str_starts_with($d, '0000')) ? date('d/m/Y H:i', strtotime($d)) : '';
        $num     = fn($v) => ($v !== null && $v !== '') ? (string)(float)$v : '';

        $statusLabels = [
            'approved'         => 'Aprobado',
            'approved_pending' => 'Aprobado (Pendiente)',
            'rejected'         => 'Rechazado',
            'corrected'        => 'Corregido',
            'pending'          => 'Pendiente',
        ];
        $typeLabels = [
            'cliente'                 => 'Cliente Natural',
            'cliente_juridica'        => 'Cliente Juridico',
            'proveedor'               => 'Proveedor Natural',
            'proveedor_juridico'      => 'Proveedor Juridico',
            'proveedor_internacional' => 'Proveedor Internacional',
            'transportista'           => 'Transportista',
        ];

        $dateFields = [
            'representante_nacimiento', 'fecha_nacimiento',
            'fecha_vinculacion_pep', 'fecha_desvinculacion_pep',
            'fecha_oficial_cumplimiento',
        ];
        $dtFields = ['approval_date', 'created_at', 'updated_at'];

        foreach ($rows as $i => $row) {
            $alt = ($i % 2 === 1);
            $t   = $alt ? 'TA' : 'T';
            $w   = $alt ? 'WA' : 'W';
            $n   = $alt ? 'NA' : 'N';

            $approvalStatus = $row['approval_status'] ?? '';
            $statusStyle = match($approvalStatus) {
                'approved', 'approved_pending' => 'Approved',
                'rejected'                     => 'Rejected',
                default                        => 'Pending',
            };

            $xml .= '<Row ss:Height="20">';

            foreach ($cols as [$field, , , $type]) {
                $val = (string)($row[$field] ?? '');

                // Transformaciones por campo
                if ($field === 'form_type') {
                    $val = $typeLabels[$val] ?? $val;
                } elseif ($field === 'approval_status') {
                    $xml .= '<Cell ss:StyleID="' . $statusStyle . '"><Data ss:Type="String">'
                          . $esc($statusLabels[$val] ?? $val)
                          . '</Data></Cell>';
                    continue;
                } elseif (in_array($field, $dateFields, true)) {
                    $val = $fmtDate($val);
                } elseif (in_array($field, $dtFields, true)) {
                    $val = $fmtDT($val);
                }

                if ($type === 'N') {
                    $numVal = $num($val);
                    if ($numVal !== '') {
                        $xml .= '<Cell ss:StyleID="' . $n . '"><Data ss:Type="Number">' . $numVal . '</Data></Cell>';
                    } else {
                        $xml .= '<Cell ss:StyleID="' . $n . '"><Data ss:Type="String"></Data></Cell>';
                    }
                } elseif ($type === 'W') {
                    $xml .= '<Cell ss:StyleID="' . $w . '"><Data ss:Type="String">' . $clean($val) . '</Data></Cell>';
                } else {
                    $xml .= '<Cell ss:StyleID="' . $t . '"><Data ss:Type="String">' . $esc($val) . '</Data></Cell>';
                }
            }

            $xml .= '</Row>';
        }

        $xml .= '</Table></Worksheet>';
        return $xml;
    }

    // ── Estilos ───────────────────────────────────────────────────────────────
    private function buildStyles(): string
    {
        $s = '<Styles>';
        $s .= $this->mkStyle('H',        '#1e293b', '#FFFFFF', true,  'Center', false);
        $s .= $this->mkStyle('T',        '',        '',        false, 'Left',   false);
        $s .= $this->mkStyle('TA',       '#f8fafc', '',        false, 'Left',   false);
        $s .= $this->mkStyle('W',        '',        '',        false, 'Left',   true);
        $s .= $this->mkStyle('WA',       '#f8fafc', '',        false, 'Left',   true);
        $s .= $this->mkStyle('N',        '',        '',        false, 'Right',  false);
        $s .= $this->mkStyle('NA',       '#f8fafc', '',        false, 'Right',  false);
        $s .= $this->mkStyle('Approved', '#22c55e', '#FFFFFF', true,  'Center', false);
        $s .= $this->mkStyle('Rejected', '#ef4444', '#FFFFFF', true,  'Center', false);
        $s .= $this->mkStyle('Pending',  '#f59e0b', '#FFFFFF', true,  'Center', false);
        $s .= '</Styles>';
        return $s;
    }

    private function mkStyle(string $id, string $bg, string $fg, bool $bold, string $hAlign, bool $wrap): string
    {
        $s  = '<Style ss:ID="' . $id . '">';
        $s .= '<Alignment ss:Horizontal="' . $hAlign . '" ss:Vertical="' . ($wrap ? 'Top' : 'Center') . '"';
        if ($wrap) $s .= ' ss:WrapText="1"';
        $s .= '/>';
        if ($bg !== '') $s .= '<Interior ss:Color="' . $bg . '" ss:Pattern="Solid"/>';
        $fa = '';
        if ($bold)      $fa .= ' ss:Bold="1"';
        if ($fg !== '') $fa .= ' ss:Color="' . $fg . '"';
        if ($fa !== '') $s .= '<Font' . $fa . '/>';
        $bw = ($id === 'H') ? '2' : '1';
        $bc = ($id === 'H') ? '' : ' ss:Color="#cccccc"';
        $s .= '<Borders>';
        foreach (['Top', 'Bottom', 'Left', 'Right'] as $pos) {
            $s .= '<Border ss:Position="' . $pos . '" ss:LineStyle="Continuous" ss:Weight="' . $bw . '"' . $bc . '/>';
        }
        $s .= '</Borders></Style>';
        return $s;
    }
}
