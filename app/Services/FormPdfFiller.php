<?php
namespace App\Services;

class FormPdfFiller
{
    private \FPDF $pdf;
    private array $d;
    private ?array $related = null;

    public function generate(array $formData, array $tempData): string
    {
        require_once __DIR__ . '/../Libraries/fpdf.php';
        $userType   = $tempData['role'] ?? $tempData['user_type'] ?? 'cliente';
        $personType = $tempData['person_type'] ?? 'natural';
        $isIntl     = !empty($formData['pais']) && strtolower($formData['pais']) !== 'colombia';
        $key        = $this->resolveKey($userType, $personType, $isIntl, $formData);
        $this->d    = $formData;
        $this->related = $tempData['related_form'] ?? null;
        $this->pdf  = new \FPDF('P', 'mm', 'A4');
        $this->pdf->SetAutoPageBreak(false);
        $this->pdf->SetMargins(5, 5, 5);
        $metaTitle = $this->pdfMetaTitle($key, $formData);
        $this->pdf->SetTitle($this->e($metaTitle));
        $this->pdf->SetSubject($this->e('Formulario SAGRILAFT'));
        $this->pdf->SetCreator('SAGRILAFT');
        $this->pdf->SetAuthor('SAGRILAFT');
        $this->pdf->AddPage();
        match($key) {
            'cliente_natural'         => $this->fgf08(),
            'cliente_juridica'        => $this->fgf16(),
            'proveedor_natural'       => $this->fco05(),
            'proveedor_juridica'      => $this->fco02(),
            'proveedor_internacional' => $this->fco04(),
            'declaracion_cliente'     => $this->fgf17(),
            'declaracion_proveedor'   => $this->fco03(),
            default                   => $this->fgf08(),
        };
        return $this->pdf->Output('S');
    }

    // =========================================================================
    // FGF-08  (Cliente Natural)
    // =========================================================================
    private function fgf08(): void
    {
        $p = $this->pdf;
        $W = 200; $h = 5.5; $hs = 5.0;

        $this->header('DIRECCIONAMIENTO ESTRATEGICO', 'CREACION DE CLIENTES-PERSONA NATURAL', '29/04/16', '10/12/2025', '06', 'FGF-08');

        $this->sectionTitle('ESPACIO PARA SER TRAMITADO POR EL DEPARTAMENTO DE CARTERA', $W, $hs);
        $this->row2('VINCULACION:', $this->v('vinculacion'), 28, 60, 'FECHA DE VINCULACION:', $this->v('fecha_vinculacion'), 42, 70, $h);
        $this->row1('ACTUALIZACION:', $this->v('actualizacion'), 28, $W - 28, $h);

        $this->sectionTitle('DATOS GENERALES', $W, $hs);
        $this->row2c('PRINCIPAL:', 100, 'SUCURSAL:', 100, $h);
        $this->row2('NOMBRE DEL CLIENTE:', $this->v('company_name'), 35, 85, 'CC:', $this->v('nit'), 10, 70, $h);
        $this->row1('NOMBRE ESTABLECIMIENTO', $this->v('nombre_establecimiento'), 40, $W - 40, $h);
        $this->row2('DIRECCION:', $this->v('address'), 20, 110, 'CIUDAD:', $this->v('ciudad'), 16, 54, $h);
        $this->row2('BARRIO:', $this->v('barrio'), 16, 70, 'LOCALIDAD:', $this->v('localidad'), 20, 94, $h);
        $this->row3('TELEFONO FIJO:', $this->v('telefono_fijo'), 24, 40, 'CELULAR:', $this->v('celular'), 16, 40, 'E-MAIL:', $this->v('email'), 14, 66, $h);
        $this->row2('LISTA DE PRECIOS:', $this->v('lista_precios'), 30, 68, 'COD. VENDEDOR', $this->v('codigo_vendedor'), 28, 74, $h);

        $this->labelCell('ACTIVIDAD ECONOMICA CODIGO CIU:', 52, $h);
        $p->SetFont('Arial', '', 5.5);
        $p->Cell(80, $h, $this->e(mb_strimwidth($this->v('codigo_ciiu'), 0, 55, '...')), 1, 0, 'L');
        $this->labelCell('CONTADO:', 18, $h);
        $p->SetFont('Arial', '', 6); $p->Cell(10, $h, $this->v('forma_pago') === 'contado' ? 'X' : '', 1, 0, 'C');
        $this->labelCell('CREDITO', 16, $h);
        $p->SetFont('Arial', '', 6); $p->Cell(24, $h, $this->v('forma_pago') === 'credito' ? 'X' : '', 1, 1, 'C');

        $this->row3('FECHA DE NACIMIENTO:', $this->v('fecha_nacimiento'), 36, 50, 'MES:', $this->v('mes_nacimiento'), 10, 40, 'DIA:', $this->v('dia_nacimiento'), 10, 54, $h);

        $p->SetFont('Arial', '', 5.5);
        $p->MultiCell($W, 3.8, $this->e('En mi calidad de funcionario de POLLO FIESTA S.A. declaro que la informacion aqui contenida corresponde con la realidad y que respondere personalmente por cualquier perjuicio que pueda sufrir POLLO FIESTA S.A. que se derive de la deliberada inexactitud de los datos ingresados en este formulario.'), 1, 'L');

        $this->row2('NOMBRE DEL VENDEDOR', $this->v('nombre_vendedor'), 40, 58, 'CLASE DE CLIENTE:', $this->v('clase_cliente'), 30, 72, $h);
        $this->row2('FIRMA', '', 14, 84, 'DESCRIPCION:', $this->v('descripcion_firma'), 24, 78, $h);

        $this->sectionTitle('INFORMACION FINANCIERA:', $W, $hs);
        $this->row2('ACTIVOS $', $this->money('activos'), 20, 58, 'INGRESOS $', $this->money('ingresos'), 20, 102, $h);
        $this->row2('PASIVO $', $this->money('pasivos'), 20, 58, 'GASTOS $', $this->money('gastos'), 20, 102, $h);
        $this->row2('PATRIMONIO $', $this->money('patrimonio'), 24, 54, 'OTROS INGRESOS $', $this->money('otros_ingresos'), 28, 94, $h);
        $p->SetFont('Arial', '', 6);
        $p->Cell($W, $h, $this->e('Detalle de otros ingresos: ' . $this->v('detalle_otros_ingresos')), 1, 1, 'L');

        $this->sectionTitle('DATOS TRIBUTARIOS', $W, $hs);
        $this->labelCell('TIPO DE CONTRIBUYENTE EN RENTA:', 52, $h);
        $p->SetFont('Arial', '', 6);
        $p->Cell(8, $h, $this->v('tipo_contribuyente') === 'persona_juridica' ? 'X' : '', 1, 0, 'C');
        $p->Cell(26, $h, $this->e('Persona Juridica'), 0, 0, 'L');
        $p->Cell(8, $h, $this->v('tipo_contribuyente') === 'gran_contribuyente' ? 'X' : '', 1, 0, 'C');
        $p->Cell(26, $h, $this->e('Gran Contribuyente'), 0, 0, 'L');
        $p->Cell(8, $h, $this->v('regimen_tributario') === 'especial' ? 'X' : '', 1, 0, 'C');
        $p->Cell(30, $h, $this->e('Regimen Tributario Especial'), 0, 0, 'L');
        $p->Cell(8, $h, $this->v('regimen_tributario') === 'no_contribuyente' ? 'X' : '', 1, 0, 'C');
        $p->Cell(0, $h, $this->e('No Contribuyente'), 0, 1, 'L');

        $this->sectionTitle('AUTORIZACION PARA CONSULTAS', $W, $hs);
        $p->SetFont('Arial', '', 6);
        $si1 = $this->v('autoriza_centrales_riesgo') === 'si' ? '[X]' : '[ ]';
        $no1 = $this->v('autoriza_centrales_riesgo') === 'no' ? '[X]' : '[ ]';
        $si2 = $this->v('autoriza_centrales') === 'si' ? '[X]' : '[ ]';
        $no2 = $this->v('autoriza_centrales') === 'no' ? '[X]' : '[ ]';
        $p->Cell($W, $h, $this->e("Autorizo consulta en listas restrictivas Nacionales e Internacionales:  Si: $si1   No: $no1"), 1, 1, 'L');
        $p->Cell($W, $h, $this->e("Autorizo consulta en Centrales de Riesgo:  Si: $si2   No: $no2"), 1, 1, 'L');
        $this->row1('FIRMA REPRESENTANTE LEGAL:', '', 50, $W - 50, 16);
        $this->row1('NOMBRE:', $this->v('representante_nombre') ?: $this->v('company_name'), 20, $W - 20, $h);

        $p->SetFont('Arial', '', 5.2);
        $p->MultiCell($W, 4.0, $this->e('AVISO DE PRIVACIDAD Y AUTORIZACION DE TRATAMIENTO DE DATOS PERSONALES: Autorizo a Pollo Fiesta S.A., identificada con Nit 860.032.450-9 para que utilice la informacion que he suministrado con los siguientes fines: Para recaudar, almacenar, utilizar, consultar y en general dar tratamiento para los fines que este documento contempla y ademas para desarrollar todas aquellas que se definen como finalidades del tratamiento de la informacion trazadas en la POLITICA DE TRATAMIENTO Y PRIVACIDAD DE LA INFORMACION DE POLLO FIESTA S.A. DE CONFORMIDAD CON LA LEY DE HABEAS DATA, disponible en la pagina web http://www.pollo-fiesta.com/, todo conforme a la Ley 1581 de 2012 y el Decreto 1377 de 2013, la Ley 1266 de 2006. Esta autorizacion incluye ademas la autorizacion de consulta y reporte en centrales de Riesgo.'), 1, 'L');

        $this->sectionTitle('ESPACIO EXCLUSIVO PARA POLLO FIESTA', $W, $hs);
        $this->labelCell('CONSULTA EN LISTAS RESTRICTIVAS OFAC:', 64, $h);
        $p->SetFont('Arial', '', 6);
        $p->Cell(8, $h, $this->v('consulta_ofac') === 'negativa' ? 'X' : '', 1, 0, 'C');
        $p->Cell(30, $h, 'CONSULTA NEGATIVA', 0, 0, 'L');
        $p->Cell(8, $h, $this->v('consulta_ofac') === 'positiva' ? 'X' : '', 1, 0, 'C');
        $p->Cell(0, $h, 'CONSULTA POSITIVA', 0, 1, 'L');
        $this->labelCell('CONSULTA EN LISTAS NACIONALES:', 54, $h);
        $p->SetFont('Arial', '', 6);
        $p->Cell(8, $h, $this->v('consulta_listas_nacionales') === 'negativa' ? 'X' : '', 1, 0, 'C');
        $p->Cell(30, $h, 'CONSULTA NEGATIVA', 0, 0, 'L');
        $p->Cell(8, $h, $this->v('consulta_listas_nacionales') === 'positiva' ? 'X' : '', 1, 0, 'C');
        $p->Cell(0, $h, 'CONSULTA POSITIVA', 0, 1, 'L');

        // RECIBE + FIRMA OFICIAL — labels con mismo alto que celdas (h=16)
        $firmaH = 16;
        $yFirma = $p->GetY();
        $p->SetXY(5 + 16, $yFirma);
        $p->SetFont('Arial', '', 6);
        $p->Cell(52, $firmaH, $this->e($this->v('recibe')), 1, 0, 'L');
        $p->SetXY(5 + 16 + 52 + 58, $yFirma);
        $p->Cell($W - 126, $firmaH, '', 1, 1, 'L');
        $this->sigImage('firma_oficial_data', 5 + 16 + 52 + 58 + 2, $yFirma + 1, 50, $firmaH - 2);
        $p->SetXY(5, $yFirma);
        $p->SetFont('Arial', 'B', 6);
        $p->Cell(16, $firmaH, $this->e('RECIBE:'), 1, 0, 'L');
        $p->SetXY(5 + 16 + 52, $yFirma);
        $p->Cell(58, $firmaH, $this->e('FIRMA DEL OFICIAL DE CUMPLIMIENTO:'), 1, 0, 'L');
        $p->SetY($yFirma + $firmaH);

        $this->row3('DIRECTOR DE CARTERA', $this->v('director_cartera'), 36, 30, 'NOMBRE:', $this->v('nombre_firmante'), 16, 50, 'GERENCIA COMERCIAL', $this->v('gerencia_comercial'), 36, 32, $h);

        // OBSERVACIONES con label del mismo alto
        $obsText = $this->e($this->v('observaciones'));
        $yObs = $p->GetY();
        $p->SetXY(5 + 28, $yObs);
        $p->SetFont('Arial', '', 6.5);
        $p->MultiCell($W - 28, 5, $obsText, 1, 'L');
        $yObsEnd = $p->GetY();
        $obsH = $yObsEnd - $yObs;
        $p->SetXY(5, $yObs);
        $p->SetFont('Arial', 'B', 6);
        $p->Cell(28, $obsH, $this->e('OBSERVACIONES:'), 1, 0, 'L');
        $p->SetY($yObsEnd);
    }

    // =========================================================================
    // FGF-16  (Cliente Juridica)
    // =========================================================================
    private function fgf16(): void
    {
        $p = $this->pdf;
        $W = 200; $h = 5.5; $hs = 5.0;

        $this->header('DIRECCIONAMIENTO ESTRATEGICO', 'CREACION DE CLIENTES - PERSONA JURIDICA', '29/04/16', '10/12/2025', '04', 'FGF-16');

        $this->sectionTitle('ESPACIO PARA SER TRAMITADO POR EL DEPARTAMENTO DE CARTERA', $W, $hs);
        $this->row2('VINCULACION:', $this->v('vinculacion'), 28, 60, 'ACTUALIZACION:', $this->v('actualizacion'), 28, 84, $h);
        $this->row1('FECHA DE VINCULACION:', $this->v('fecha_vinculacion'), 40, $W - 40, $h);

        $this->sectionTitle('DATOS GENERALES', $W, $hs);
        $this->row2c('PRINCIPAL:', 100, 'SUCURSAL:', 100, $h);
        $this->row3('RAZON SOCIAL:', $this->v('company_name'), 24, 76, 'NIT:', $this->v('nit'), 10, 40, 'D.V.:', $this->v('dv'), 10, 40, $h);
        $this->row2('RUT:', $this->v('rut'), 10, 60, 'ESTABLECIMIENTO:', $this->v('nombre_establecimiento'), 28, 102, $h);
        $this->row2('DOMICILIO:', $this->v('address'), 20, 110, 'CIUDAD:', $this->v('ciudad'), 16, 54, $h);
        $this->row3('BARRIO:', $this->v('barrio'), 16, 54, 'LOCALIDAD:', $this->v('localidad'), 20, 54, 'SUCURSAL:', $this->v('sucursal'), 18, 38, $h);
        $this->row3('TEL. FIJO:', $this->v('telefono_fijo'), 18, 40, 'CELULAR:', $this->v('celular'), 16, 40, 'CORREO:', $this->v('email'), 16, 70, $h);
        $this->row2('LISTA PRECIOS:', $this->v('lista_precios'), 26, 48, 'COD. VENDEDOR:', $this->v('codigo_vendedor'), 26, 48, $h);

        $this->labelCell('ACTIVIDAD ECON.:', 30, $h);
        $p->SetFont('Arial', '', 5.5);
        $p->Cell(110, $h, $this->e(mb_strimwidth($this->v('codigo_ciiu'), 0, 70, '...')), 1, 0, 'L');
        $this->labelCell('CONTADO:', 16, $h);
        $p->SetFont('Arial', '', 6); $p->Cell(10, $h, $this->v('forma_pago') === 'contado' ? 'X' : '', 1, 0, 'C');
        $this->labelCell('CREDITO:', 14, $h);
        $p->SetFont('Arial', '', 6); $p->Cell(20, $h, $this->v('forma_pago') === 'credito' ? 'X' : '', 1, 1, 'C');

        $this->sectionTitle('DATOS DEL REPRESENTANTE LEGAL', $W, $hs);
        $this->row3('NOMBRE:', $this->v('representante_nombre'), 16, 74, 'TIPO DOC:', $this->v('representante_tipo_doc'), 18, 30, 'NUMERO:', $this->v('representante_documento'), 16, 46, $h);
        $this->row3('PROFESION:', $this->v('representante_profesion'), 20, 50, 'F. NACIMIENTO:', $this->v('representante_nacimiento'), 26, 40, 'TELEFONO:', $this->v('representante_telefono'), 18, 46, $h);
        $this->row1('CORREO:', $this->v('representante_email'), 16, $W - 16, $h);

        $this->sectionTitle('INFORMACION FINANCIERA', $W, $hs);
        $this->row2('ACTIVOS $', $this->money('activos'), 20, 58, 'INGRESOS $', $this->money('ingresos'), 20, 102, $h);
        $this->row2('PASIVO $', $this->money('pasivos'), 20, 58, 'GASTOS $', $this->money('gastos'), 20, 102, $h);
        $this->row2('PATRIMONIO $', $this->money('patrimonio'), 24, 54, 'OTROS INGRESOS $', $this->money('otros_ingresos'), 28, 94, $h);

        $this->sectionTitle('COMPOSICION ACCIONARIA', $W, $hs);
        // Cabecera tabla accionistas
        $p->SetFont('Arial', 'B', 6);
        $p->SetFillColor(220, 220, 220);
        $p->Cell(70, $h, $this->e('NOMBRE / RAZON SOCIAL'), 1, 0, 'C', true);
        $p->Cell(40, $h, $this->e('DOCUMENTO'), 1, 0, 'C', true);
        $p->Cell(30, $h, $this->e('% PARTICIPACION'), 1, 0, 'C', true);
        $p->Cell(60, $h, $this->e('NACIONALIDAD'), 1, 1, 'C', true);
        $p->SetFillColor(255, 255, 255);
        // Filas accionistas
        $nombres = (array)($this->d['accionista_nombre'] ?? []);
        $docs    = (array)($this->d['accionista_documento'] ?? []);
        $parts   = (array)($this->d['accionista_participacion'] ?? []);
        $nacs    = (array)($this->d['accionista_nacionalidad'] ?? []);
        $rows = max(count($nombres), 1);
        for ($i = 0; $i < $rows; $i++) {
            $p->SetFont('Arial', '', 6);
            $p->Cell(70, $h, $this->e($nombres[$i] ?? ''), 1, 0, 'L');
            $p->Cell(40, $h, $this->e($docs[$i] ?? ''), 1, 0, 'L');
            $p->Cell(30, $h, $this->e(isset($parts[$i]) ? $parts[$i] . '%' : ''), 1, 0, 'C');
            $p->Cell(60, $h, $this->e($nacs[$i] ?? ''), 1, 1, 'L');
        }

        $this->sectionTitle('DATOS TRIBUTARIOS', $W, $hs);
        $this->labelCell('TIPO CONTRIBUYENTE:', 36, $h);
        $p->SetFont('Arial', '', 6);
        $p->Cell(8, $h, $this->v('tipo_contribuyente') === 'persona_juridica' ? 'X' : '', 1, 0, 'C');
        $p->Cell(30, $h, $this->e('Persona Juridica'), 0, 0, 'L');
        $p->Cell(8, $h, $this->v('tipo_contribuyente') === 'gran_contribuyente' ? 'X' : '', 1, 0, 'C');
        $p->Cell(30, $h, $this->e('Gran Contribuyente'), 0, 0, 'L');
        $this->labelCell('REGIMEN:', 16, $h);
        $p->Cell(8, $h, $this->v('regimen_tributario') === 'especial' ? 'X' : '', 1, 0, 'C');
        $p->Cell(30, $h, $this->e('Especial'), 0, 0, 'L');
        $p->Cell(8, $h, $this->v('regimen_tributario') === 'ordinario' ? 'X' : '', 1, 0, 'C');
        $p->Cell(0, $h, $this->e('Ordinario'), 0, 1, 'L');

        $this->sectionTitle('AUTORIZACION PARA CONSULTAS', $W, $hs);
        $p->SetFont('Arial', '', 6);
        $si1 = $this->v('autoriza_centrales_riesgo') === 'si' ? '[X]' : '[ ]';
        $no1 = $this->v('autoriza_centrales_riesgo') === 'no' ? '[X]' : '[ ]';
        $si2 = $this->v('autoriza_centrales') === 'si' ? '[X]' : '[ ]';
        $no2 = $this->v('autoriza_centrales') === 'no' ? '[X]' : '[ ]';
        $p->Cell($W, $h, $this->e("Autorizo consulta en listas restrictivas:  Si: $si1   No: $no1   | Centrales de Riesgo:  Si: $si2   No: $no2"), 1, 1, 'L');

        $p->SetFont('Arial', '', 5.2);
        $p->MultiCell($W, 3.8, $this->e('AVISO DE PRIVACIDAD: Autorizo a Pollo Fiesta S.A. (NIT 860.032.450-9) para tratamiento de datos segun Ley 1581/2012. Politica en www.pollo-fiesta.com'), 1, 'L');

        $this->sectionTitle('ESPACIO EXCLUSIVO PARA POLLO FIESTA', $W, $hs);
        $this->labelCell('CONSULTA EN LISTAS RESTRICTIVAS OFAC:', 64, $h);
        $p->SetFont('Arial', '', 6);
        $p->Cell(8, $h, $this->v('consulta_ofac') === 'negativa' ? 'X' : '', 1, 0, 'C');
        $p->Cell(30, $h, 'CONSULTA NEGATIVA', 0, 0, 'L');
        $p->Cell(8, $h, $this->v('consulta_ofac') === 'positiva' ? 'X' : '', 1, 0, 'C');
        $p->Cell(0, $h, 'CONSULTA POSITIVA', 0, 1, 'L');
        $this->labelCell('CONSULTA EN LISTAS NACIONALES:', 54, $h);
        $p->SetFont('Arial', '', 6);
        $p->Cell(8, $h, $this->v('consulta_listas_nacionales') === 'negativa' ? 'X' : '', 1, 0, 'C');
        $p->Cell(30, $h, 'CONSULTA NEGATIVA', 0, 0, 'L');
        $p->Cell(8, $h, $this->v('consulta_listas_nacionales') === 'positiva' ? 'X' : '', 1, 0, 'C');
        $p->Cell(0, $h, 'CONSULTA POSITIVA', 0, 1, 'L');

        // RECIBE + FIRMA OFICIAL — mismo alto
        $firmaH = 16;
        $yFirma = $p->GetY();
        $p->SetXY(5 + 16, $yFirma);
        $p->SetFont('Arial', '', 6);
        $p->Cell(52, $firmaH, $this->e($this->v('recibe')), 1, 0, 'L');
        $p->SetXY(5 + 16 + 52 + 58, $yFirma);
        $p->Cell($W - 126, $firmaH, '', 1, 1, 'L');
        $this->sigImage('firma_oficial_data', 5 + 16 + 52 + 58 + 2, $yFirma + 1, 50, $firmaH - 2);
        $p->SetXY(5, $yFirma);
        $p->SetFont('Arial', 'B', 6);
        $p->Cell(16, $firmaH, $this->e('RECIBE:'), 1, 0, 'L');
        $p->SetXY(5 + 16 + 52, $yFirma);
        $p->Cell(58, $firmaH, $this->e('FIRMA DEL OFICIAL DE CUMPLIMIENTO:'), 1, 0, 'L');
        $p->SetY($yFirma + $firmaH);

        $this->row2('DIRECTOR DE CARTERA:', $this->v('director_cartera'), 36, 64, 'GERENCIA COMERCIAL:', $this->v('gerencia_comercial'), 36, 64, $h);

        // OBSERVACIONES
        $obsText = $this->e($this->v('observaciones'));
        $yObs = $p->GetY();
        $p->SetXY(5 + 28, $yObs);
        $p->SetFont('Arial', '', 6.5);
        $p->MultiCell($W - 28, 5, $obsText, 1, 'L');
        $yObsEnd = $p->GetY();
        $obsH = $yObsEnd - $yObs;
        $p->SetXY(5, $yObs);
        $p->SetFont('Arial', 'B', 6);
        $p->Cell(28, $obsH, $this->e('OBSERVACIONES:'), 1, 0, 'L');
        $p->SetY($yObsEnd);
    }

    // =========================================================================
    // FCO-05  (Proveedor Natural)
    // =========================================================================
    private function fco05(): void
    {
        $p = $this->pdf;
        $W = 200; $h = 5.5; $hs = 5.0;

        $this->header('CONOCIMIENTO DE PROVEEDOR', 'PERSONA NATURAL', '01/01/18', '10/12/2025', '03', 'FCO-05');

        $this->sectionTitle('ESPACIO PARA SER TRAMITADO POR EL DEPARTAMENTO DE COMPRAS', $W, $hs);
        $this->row2('VINCULACION:', $this->v('vinculacion'), 28, 60, 'FECHA DE VINCULACION:', $this->v('fecha_vinculacion'), 42, 70, $h);
        $this->row1('ACTUALIZACION:', $this->v('actualizacion'), 28, $W - 28, $h);

        $this->sectionTitle('DATOS DEL PROVEEDOR', $W, $hs);
        $this->row3('NOMBRE:', $this->v('company_name'), 18, 80, 'CEDULA:', $this->v('nit'), 16, 40, 'RUT:', $this->v('rut'), 10, 36, $h);
        $this->row2('DIRECCION:', $this->v('address'), 20, 110, 'CIUDAD:', $this->v('ciudad'), 16, 54, $h);
        $this->row3('TELEFONO:', $this->v('telefono_fijo') ?: $this->v('celular'), 18, 46, 'FAX:', $this->v('fax'), 10, 40, 'CORREO:', $this->v('email'), 16, 70, $h);

        $this->sectionTitle('ACTIVIDAD ECONOMICA PRINCIPAL', $W, $hs);
        $this->row1('ACTIVIDAD (CIIU):', $this->v('codigo_ciiu'), 30, $W - 30, $h);
        $this->row1('OBJETO SOCIAL:', $this->v('objeto_social'), 26, $W - 26, $h);

        $this->sectionTitle('DECLARACION ORIGEN DE LOS FONDOS', $W, $hs);
        $this->row3('NOMBRE:', $this->v('representante_nombre'), 18, 60, 'DOCUMENTO:', $this->v('representante_documento'), 22, 36, 'TIPO:', $this->v('representante_tipo_doc'), 12, 52, $h);
        $this->row3('PROFESION:', $this->v('representante_profesion'), 20, 50, 'NACIMIENTO:', $this->v('representante_lugar_nacimiento'), 22, 50, 'TELEFONO:', $this->v('representante_telefono'), 18, 40, $h);
        $this->row1('RESIDENCIA:', $this->v('representante_residencia'), 22, $W - 22, $h);

        $this->sectionTitle('INFORMACION FINANCIERA', $W, $hs);
        $this->row3('ACTIVOS $', $this->money('activos'), 18, 40, 'PASIVOS $', $this->money('pasivos'), 18, 40, 'PATRIMONIO $', $this->money('patrimonio'), 22, 62, $h);
        $this->row3('INGRESOS $', $this->money('ingresos'), 18, 40, 'GASTOS $', $this->money('gastos'), 18, 40, 'OTROS INGRESOS $', $this->money('otros_ingresos'), 26, 58, $h);
        $this->row1('DETALLE OTROS INGRESOS:', $this->v('detalle_otros_ingresos'), 38, $W - 38, $h);

        $p->SetFont('Arial', '', 5.2);
        $p->MultiCell($W, 3.5, $this->e('AVISO DE PRIVACIDAD Y AUTORIZACION DE TRATAMIENTO DE DATOS PERSONALES: Autorizo a Pollo Fiesta S.A., identificada con Nit 860.032.450-9 para que utilice la informacion que he suministrado con los siguientes fines: Para recaudar, almacenar, utilizar, consultar y en general dar tratamiento para los fines que este documento contempla y ademas para desarrollar todas aquellas que se definen como finalidades del tratamiento de la informacion trazadas en la POLITICA DE TRATAMIENTO Y PRIVACIDAD DE LA INFORMACION DE POLLO FIESTA S.A. DE CONFORMIDAD CON LA LEY DE HABEAS DATA, disponible en la pagina web http://www.pollo-fiesta.com/, todo conforme a la Ley 1581 de 2012 y el Decreto 1377 de 2013, la Ley 1266 de 2006. Esta autorizacion incluye ademas la autorizacion de consulta y reporte en centrales de Riesgo.'), 1, 'L');

        $this->sectionTitle('ESPACIO EXCLUSIVO PARA POLLO FIESTA', $W, $hs);
        $this->labelCell('CONSULTA OFAC:', 30, $h);
        $p->SetFont('Arial', '', 6);
        $p->Cell(8, $h, $this->v('consulta_ofac') === 'negativa' ? 'X' : '', 1, 0, 'C');
        $p->Cell(28, $h, 'NEGATIVA', 0, 0, 'L');
        $p->Cell(8, $h, $this->v('consulta_ofac') === 'positiva' ? 'X' : '', 1, 0, 'C');
        $p->Cell(0, $h, 'POSITIVA', 0, 1, 'L');
        $this->labelCell('CONSULTA ONU:', 30, $h);
        $p->SetFont('Arial', '', 6);
        $p->Cell(8, $h, $this->v('consulta_onu') === 'negativa' ? 'X' : '', 1, 0, 'C');
        $p->Cell(28, $h, 'NEGATIVA', 0, 0, 'L');
        $p->Cell(8, $h, $this->v('consulta_onu') === 'positiva' ? 'X' : '', 1, 0, 'C');
        $p->Cell(0, $h, 'POSITIVA', 0, 1, 'L');
        $this->row1('NOMBRE OFICIAL:', $this->v('nombre_oficial'), 30, $W - 30, $h);

        // FIRMA OFICIAL — mismo alto
        $firmaH = 18;
        $yFirma = $p->GetY();
        $p->SetXY(5 + 26, $yFirma);
        $p->Cell($W - 26, $firmaH, '', 1, 1, 'L');
        $this->sigImage('firma_oficial_data', 5 + 26 + 2, $yFirma + 1, 50, $firmaH - 2);
        $p->SetXY(5, $yFirma);
        $p->SetFont('Arial', 'B', 6);
        $p->Cell(26, $firmaH, $this->e('FIRMA OFICIAL:'), 1, 0, 'L');
        $p->SetY($yFirma + $firmaH);

        // OBSERVACIONES
        $obsText = $this->e($this->v('observaciones'));
        $yObs = $p->GetY();
        $p->SetXY(5 + 28, $yObs);
        $p->SetFont('Arial', '', 6.5);
        $p->MultiCell($W - 28, 5, $obsText, 1, 'L');
        $yObsEnd = $p->GetY();
        $obsH = $yObsEnd - $yObs;
        $p->SetXY(5, $yObs);
        $p->SetFont('Arial', 'B', 6);
        $p->Cell(28, $obsH, $this->e('OBSERVACIONES:'), 1, 0, 'L');
        $p->SetY($yObsEnd);
    }

    // =========================================================================
    // FCO-02  (Proveedor Juridica)
    // =========================================================================
    private function fco02(): void
    {
        $p = $this->pdf;
        $W = 200; $h = 5.5; $hs = 5.0;

        $this->header('CONOCIMIENTO DE PROVEEDOR', 'PERSONA JURIDICA NACIONAL', '01/01/18', '10/12/2025', '03', 'FCO-02');

        $this->sectionTitle('ESPACIO PARA SER TRAMITADO POR EL DEPARTAMENTO DE COMPRAS', $W, $hs);
        $this->row2('VINCULACION:', $this->v('vinculacion'), 28, 60, 'ACTUALIZACION:', $this->v('actualizacion'), 28, 84, $h);
        $this->row1('FECHA DE VINCULACION:', $this->v('fecha_vinculacion'), 40, $W - 40, $h);

        $this->sectionTitle('DATOS GENERALES DE LA EMPRESA', $W, $hs);
        $this->row3('RAZON SOCIAL:', $this->v('company_name'), 24, 76, 'NIT:', $this->v('nit'), 10, 30, 'D.V.:', $this->v('dv'), 10, 50, $h);
        $this->row2('RUT:', $this->v('rut'), 10, 60, 'TEL. FIJO:', $this->v('telefono_fijo'), 18, 112, $h);
        $this->row3('FAX:', $this->v('fax'), 10, 50, 'CORREO:', $this->v('email'), 16, 84, 'CIUDAD:', $this->v('ciudad'), 16, 24, $h);
        $this->row1('DIRECCION:', $this->v('address'), 20, $W - 20, $h);
        $this->labelCell('TIPO COMPANIA:', 26, $h);
        $p->SetFont('Arial', '', 6);
        $p->Cell(8, $h, $this->v('tipo_compania') === 'privada' ? 'X' : '', 1, 0, 'C');
        $p->Cell(22, $h, 'PRIVADA', 0, 0, 'L');
        $p->Cell(8, $h, $this->v('tipo_compania') === 'publica' ? 'X' : '', 1, 0, 'C');
        $p->Cell(22, $h, $this->e('PUBLICA'), 0, 0, 'L');
        $p->Cell(8, $h, $this->v('tipo_compania') === 'mixta' ? 'X' : '', 1, 0, 'C');
        $p->Cell(0, $h, 'MIXTA', 0, 1, 'L');

        $this->sectionTitle('ACTIVIDAD ECONOMICA PRINCIPAL DE LA EMPRESA', $W, $hs);
        $this->row1('ACTIVIDAD (CIIU):', $this->v('codigo_ciiu'), 30, $W - 30, $h);
        // Objeto social con MultiCell
        $objText = $this->e($this->v('objeto_social'));
        $yObj = $p->GetY();
        $p->SetXY(5 + 26, $yObj);
        $p->SetFont('Arial', '', 6.5);
        $p->MultiCell($W - 26, 5, $objText, 1, 'L');
        $yObjEnd = $p->GetY();
        $objH = $yObjEnd - $yObj;
        $p->SetXY(5, $yObj);
        $p->SetFont('Arial', 'B', 6);
        $p->Cell(26, $objH, $this->e('OBJETO SOCIAL:'), 1, 0, 'L');
        $p->SetY($yObjEnd);

        $this->sectionTitle('DATOS DEL REPRESENTANTE LEGAL Y/O APODERADO', $W, $hs);
        $this->row3('NOMBRE:', $this->v('representante_nombre'), 16, 60, 'DOC:', $this->v('representante_documento'), 10, 40, 'PROFESION:', $this->v('representante_profesion'), 20, 54, $h);
        $this->row3('NACIMIENTO:', $this->v('representante_lugar_nacimiento'), 22, 50, 'TELEFONO:', $this->v('representante_telefono'), 18, 40, 'RESIDENCIA:', $this->v('representante_residencia'), 20, 50, $h);

        $this->sectionTitle('COMPOSICION ACCIONARIA', $W, $hs);
        $p->SetFont('Arial', 'B', 6);
        $p->SetFillColor(220, 220, 220);
        $p->Cell(100, $h, $this->e('NOMBRE / RAZON SOCIAL'), 1, 0, 'C', true);
        $p->Cell(50, $h, 'CC', 1, 0, 'C', true);
        $p->Cell(50, $h, 'CE', 1, 1, 'C', true);
        $p->SetFillColor(255, 255, 255);
        $nombres = (array)($this->d['accionista_nombre'] ?? []);
        $ccs     = (array)($this->d['accionista_cc'] ?? []);
        $ces     = (array)($this->d['accionista_ce'] ?? []);
        $rows = max(count($nombres), 1);
        for ($i = 0; $i < $rows; $i++) {
            $p->SetFont('Arial', '', 6);
            $p->Cell(100, $h, $this->e($nombres[$i] ?? ''), 1, 0, 'L');
            $p->Cell(50, $h, $this->e($ccs[$i] ?? ''), 1, 0, 'L');
            $p->Cell(50, $h, $this->e($ces[$i] ?? ''), 1, 1, 'L');
        }

        $this->sectionTitle('INFORMACION FINANCIERA', $W, $hs);
        $this->row3('ACTIVOS $', $this->money('activos'), 18, 30, 'PASIVO $', $this->money('pasivos'), 18, 30, 'PATRIMONIO $', $this->money('patrimonio'), 22, 82, $h);
        $this->row2('INGRESOS $', $this->money('ingresos'), 18, 50, 'GASTOS $', $this->money('gastos'), 18, 50, $h);
        $this->row2('OTROS ING. $', $this->money('otros_ingresos'), 22, 46, 'DETALLE:', $this->v('detalle_otros_ingresos'), 16, 116, $h);

        $this->sectionTitle('CERTIFICACIONES Y DATOS TRIBUTARIOS', $W, $hs);
        $this->row1('CERTIFICACION:', $this->v('certificacion'), 26, $W - 26, $h);
        $this->labelCell('TIPO CONTRIBUYENTE:', 36, $h);
        $p->SetFont('Arial', '', 6);
        $p->Cell(8, $h, $this->v('tipo_contribuyente') === 'persona_juridica' ? 'X' : '', 1, 0, 'C');
        $p->Cell(30, $h, $this->e('Persona Juridica'), 0, 0, 'L');
        $p->Cell(8, $h, $this->v('tipo_contribuyente') === 'gran_contribuyente' ? 'X' : '', 1, 0, 'C');
        $p->Cell(30, $h, $this->e('Gran Contribuyente'), 0, 0, 'L');
        $this->labelCell('REGIMEN:', 16, $h);
        $p->Cell(8, $h, $this->v('regimen_tributario') === 'especial' ? 'X' : '', 1, 0, 'C');
        $p->Cell(30, $h, 'Especial', 0, 0, 'L');
        $p->Cell(8, $h, $this->v('regimen_tributario') === 'no_contribuyente' ? 'X' : '', 1, 0, 'C');
        $p->Cell(0, $h, $this->e('No Contribuyente'), 0, 1, 'L');

        $p->SetFont('Arial', '', 5.2);
        $p->MultiCell($W, 3.8, $this->e('AVISO DE PRIVACIDAD: Autorizo a Pollo Fiesta S.A. (NIT 860.032.450-9) para tratamiento de datos segun Ley 1581/2012. Politica en www.pollo-fiesta.com'), 1, 'L');

        $this->sectionTitle('ESPACIO EXCLUSIVO PARA POLLO FIESTA', $W, $hs);
        $this->labelCell('CONSULTA OFAC:', 30, $h);
        $p->SetFont('Arial', '', 6);
        $p->Cell(8, $h, $this->v('consulta_ofac') === 'negativa' ? 'X' : '', 1, 0, 'C');
        $p->Cell(28, $h, 'NEGATIVA', 0, 0, 'L');
        $p->Cell(8, $h, $this->v('consulta_ofac') === 'positiva' ? 'X' : '', 1, 0, 'C');
        $p->Cell(0, $h, 'POSITIVA', 0, 1, 'L');
        $this->labelCell('CONSULTA ONU:', 30, $h);
        $p->SetFont('Arial', '', 6);
        $p->Cell(8, $h, $this->v('consulta_onu') === 'negativa' ? 'X' : '', 1, 0, 'C');
        $p->Cell(28, $h, 'NEGATIVA', 0, 0, 'L');
        $p->Cell(8, $h, $this->v('consulta_onu') === 'positiva' ? 'X' : '', 1, 0, 'C');
        $p->Cell(0, $h, 'POSITIVA', 0, 1, 'L');
        $this->row1('PREPARO:', $this->v('preparo'), 20, $W - 20, $h);
        $this->row1('NOMBRE:', $this->v('nombre_oficial'), 20, $W - 20, $h);
        $this->row1('REVISO:', $this->v('reviso'), 20, $W - 20, $h);

        // FIRMA OFICIAL CUMPLIMIENTO — mismo alto
        $firmaH = 16;
        $yFirma = $p->GetY();
        $p->SetXY(5 + 58, $yFirma);
        $p->Cell($W - 58, $firmaH, '', 1, 1, 'L');
        $this->sigImage('firma_oficial_cumplimiento_data', 5 + 58 + 2, $yFirma + 1, 50, $firmaH - 2);
        $p->SetXY(5, $yFirma);
        $p->SetFont('Arial', 'B', 6);
        $p->Cell(58, $firmaH, $this->e('FIRMA DEL OFICIAL DE CUMPLIMIENTO:'), 1, 0, 'L');
        $p->SetY($yFirma + $firmaH);

        // OBSERVACIONES
        $obsText = $this->e($this->v('observaciones'));
        $yObs = $p->GetY();
        $p->SetXY(5 + 28, $yObs);
        $p->SetFont('Arial', '', 6.5);
        $p->MultiCell($W - 28, 5, $obsText, 1, 'L');
        $yObsEnd = $p->GetY();
        $obsH = $yObsEnd - $yObs;
        $p->SetXY(5, $yObs);
        $p->SetFont('Arial', 'B', 6);
        $p->Cell(28, $obsH, $this->e('OBSERVACIONES:'), 1, 0, 'L');
        $p->SetY($yObsEnd);
    }

    // =========================================================================
    // FCO-04  (Proveedor Internacional)
    // =========================================================================
    private function fco04(): void
    {
        $p = $this->pdf;
        $W = 200; $h = 5.5; $hs = 5.0;

        $this->header('CONOCIMIENTO DE PROVEEDOR', 'PROVEEDOR INTERNACIONAL', '01/01/18', '10/12/2025', '02', 'FCO-04');

        $this->sectionTitle('ESPACIO PARA SER TRAMITADO POR EL DEPARTAMENTO DE COMPRAS', $W, $hs);
        $this->row2('VINCULACION:', $this->v('vinculacion'), 28, 60, 'ACTUALIZACION:', $this->v('actualizacion'), 28, 84, $h);
        $this->row1('FECHA DE VINCULACION:', $this->v('fecha_vinculacion'), 40, $W - 40, $h);

        $this->sectionTitle('DATOS GENERALES DEL PROVEEDOR', $W, $hs);
        $this->row1('RAZON SOCIAL:', $this->v('company_name'), 28, $W - 28, $h);
        $this->row1('NUMERO REGISTRO:', $this->v('numero_registro') ?: $this->v('nit'), 30, $W - 30, $h);
        $this->row2('PAIS:', $this->v('pais'), 12, 88, 'CIUDAD:', $this->v('ciudad'), 16, 84, $h);
        $this->row1('DIRECCION:', $this->v('address'), 20, $W - 20, $h);
        $this->row2('TELEFONO:', $this->v('telefono_fijo') ?: $this->v('celular'), 18, 62, 'FAX:', $this->v('fax'), 10, 110, $h);
        $this->row1('CORREO:', $this->v('email'), 16, $W - 16, $h);
        $this->row1('SITIO WEB:', $this->v('sitio_web'), 20, $W - 20, $h);

        $this->sectionTitle('ACTIVIDAD ECONOMICA PRINCIPAL', $W, $hs);
        $this->row1('ACTIVIDAD (CIIU):', $this->v('codigo_ciiu'), 30, $W - 30, $h);
        $this->row1('OBJETO SOCIAL:', $this->v('objeto_social'), 26, $W - 26, $h);
        $this->row1('PRODUCTOS/SERVICIOS:', $this->v('productos_servicios'), 36, $W - 36, $h);

        $this->sectionTitle('DATOS DEL REPRESENTANTE LEGAL', $W, $hs);
        $this->row1('NOMBRE:', $this->v('representante_nombre'), 16, $W - 16, $h);
        $this->row2('NUMERO DOCUMENTO:', $this->v('representante_documento'), 34, 66, 'NACIONALIDAD:', $this->v('representante_nacionalidad'), 26, 74, $h);
        $this->row2('CARGO:', $this->v('representante_cargo'), 16, 64, 'TELEFONO:', $this->v('representante_telefono'), 18, 102, $h);
        $this->row1('CORREO:', $this->v('representante_email'), 16, $W - 16, $h);

        $this->sectionTitle('COMPOSICION ACCIONARIA', $W, $hs);
        $p->SetFont('Arial', 'B', 6);
        $p->SetFillColor(220, 220, 220);
        $p->Cell(80, $h, $this->e('NOMBRE / RAZON SOCIAL'), 1, 0, 'C', true);
        $p->Cell(60, $h, 'DOCUMENTO', 1, 0, 'C', true);
        $p->Cell(60, $h, 'NACIONALIDAD', 1, 1, 'C', true);
        $p->SetFillColor(255, 255, 255);
        $nombres = (array)($this->d['accionista_nombre'] ?? []);
        $docs    = (array)($this->d['accionista_documento'] ?? []);
        $nacs    = (array)($this->d['accionista_nacionalidad'] ?? []);
        $rows = max(count($nombres), 1);
        for ($i = 0; $i < $rows; $i++) {
            $p->SetFont('Arial', '', 6);
            $p->Cell(80, $h, $this->e($nombres[$i] ?? ''), 1, 0, 'L');
            $p->Cell(60, $h, $this->e($docs[$i] ?? ''), 1, 0, 'L');
            $p->Cell(60, $h, $this->e($nacs[$i] ?? ''), 1, 1, 'L');
        }

        $this->sectionTitle('INFORMACION FINANCIERA (USD)', $W, $hs);
        $this->row2('ACTIVOS $', $this->money('activos'), 20, 58, 'INGRESOS $', $this->money('ingresos'), 20, 102, $h);
        $this->row2('PASIVO $', $this->money('pasivos'), 20, 58, 'GASTOS $', $this->money('gastos'), 20, 102, $h);
        $this->row1('PATRIMONIO $', $this->money('patrimonio'), 24, $W - 24, $h);

        $this->sectionTitle('INFORMACION DE IMPORTACION', $W, $hs);
        $this->row2('INCOTERM:', $this->v('incoterm'), 20, 60, 'FORMA DE PAGO:', $this->v('forma_pago_internacional'), 26, 94, $h);
        $this->row2('TIEMPO ENTREGA:', $this->v('tiempo_entrega'), 28, 52, 'PUERTO ORIGEN:', $this->v('puerto_origen'), 26, 94, $h);
        $this->row1('AGENTE ADUANAL:', $this->v('agente_aduanal'), 30, $W - 30, $h);

        $this->sectionTitle('CERTIFICACIONES Y DOCUMENTOS', $W, $hs);
        $this->row1('CERTIFICACIONES:', $this->v('certificaciones'), 30, $W - 30, $h);
        $this->labelCell('CERTIFICADO DE ORIGEN:', 40, $h);
        $p->SetFont('Arial', '', 6);
        $p->Cell(8, $h, $this->v('certificado_origen') === 'si' ? 'X' : '', 1, 0, 'C');
        $p->Cell(20, $h, 'SI', 0, 0, 'L');
        $p->Cell(8, $h, $this->v('certificado_origen') === 'no' ? 'X' : '', 1, 0, 'C');
        $p->Cell(0, $h, 'NO', 0, 1, 'L');

        $this->sectionTitle('ESPACIO EXCLUSIVO PARA POLLO FIESTA', $W, $hs);
        $this->labelCell('CONSULTA OFAC:', 30, $h);
        $p->SetFont('Arial', '', 6);
        $p->Cell(8, $h, $this->v('consulta_ofac') === 'negativa' ? 'X' : '', 1, 0, 'C');
        $p->Cell(28, $h, 'NEGATIVA', 0, 0, 'L');
        $p->Cell(8, $h, $this->v('consulta_ofac') === 'positiva' ? 'X' : '', 1, 0, 'C');
        $p->Cell(0, $h, 'POSITIVA', 0, 1, 'L');
        $this->labelCell('CONSULTA ONU:', 30, $h);
        $p->SetFont('Arial', '', 6);
        $p->Cell(8, $h, $this->v('consulta_onu') === 'negativa' ? 'X' : '', 1, 0, 'C');
        $p->Cell(28, $h, 'NEGATIVA', 0, 0, 'L');
        $p->Cell(8, $h, $this->v('consulta_onu') === 'positiva' ? 'X' : '', 1, 0, 'C');
        $p->Cell(0, $h, 'POSITIVA', 0, 1, 'L');
        $this->labelCell('CONSULTA INTERPOL:', 34, $h);
        $p->SetFont('Arial', '', 6);
        $p->Cell(8, $h, $this->v('consulta_interpol') === 'negativa' ? 'X' : '', 1, 0, 'C');
        $p->Cell(28, $h, 'NEGATIVA', 0, 0, 'L');
        $p->Cell(8, $h, $this->v('consulta_interpol') === 'positiva' ? 'X' : '', 1, 0, 'C');
        $p->Cell(0, $h, 'POSITIVA', 0, 1, 'L');
        $this->row1('NOMBRE OFICIAL:', $this->v('nombre_oficial'), 30, $W - 30, $h);

        // FIRMA OFICIAL — mismo alto
        $firmaH = 16;
        $yFirma = $p->GetY();
        $p->SetXY(5 + 58, $yFirma);
        $p->Cell($W - 58, $firmaH, '', 1, 1, 'L');
        $this->sigImage('firma_oficial_data', 5 + 58 + 2, $yFirma + 1, 50, $firmaH - 2);
        $p->SetXY(5, $yFirma);
        $p->SetFont('Arial', 'B', 6);
        $p->Cell(58, $firmaH, $this->e('FIRMA DEL OFICIAL DE CUMPLIMIENTO:'), 1, 0, 'L');
        $p->SetY($yFirma + $firmaH);

        // OBSERVACIONES
        $obsText = $this->e($this->v('observaciones'));
        $yObs = $p->GetY();
        $p->SetXY(5 + 28, $yObs);
        $p->SetFont('Arial', '', 6.5);
        $p->MultiCell($W - 28, 5, $obsText, 1, 'L');
        $yObsEnd = $p->GetY();
        $obsH = $yObsEnd - $yObs;
        $p->SetXY(5, $yObs);
        $p->SetFont('Arial', 'B', 6);
        $p->Cell(28, $obsH, $this->e('OBSERVACIONES:'), 1, 0, 'L');
        $p->SetY($yObsEnd);
    }

    // =========================================================================
    // FGF-17  (Declaracion Origen Fondos - Clientes)
    // =========================================================================
    private function fgf17(): void
    {
        $p = $this->pdf;
        $W = 200;

        // Mantener solo la información y estructura del formato original
        $this->header(
            'GESTION DE COMPRAS',
            'DECLARACION ORIGEN DE FONDOS',
            '09/02/16',
            '26/02/18',
            '3',
            'FGF-17'
        );
    
        $ciudad  = $this->vRelated('ciudad') ?: $this->v('ciudad');
        $empresa = $this->vRelated('company_name') ?: $this->vRelated('razon_social') ?: $this->vRelated('empresa') ?: $this->v('company_name');
        $nit     = $this->vRelated('nit') ?: $this->vRelated('numero_documento') ?: $this->vRelated('cc');
        $rep     = $this->vRelated('representante_nombre') ?: $this->v('nombre_declarante') ?: $empresa;

        $origen  = $this->v('origen_fondos') ?: $this->v('origen_recursos');
        $esPep      = $this->yn($this->v('es_pep'));
        $familiarPep = $this->yn($this->v('familiar_pep') ?: $this->v('familiares_pep') ?: $this->v('relacion_pep'));

        $x = 12;              // margen visual como original
        $contentW = 184;      // ancho útil

        $p->Ln(8);
        $p->SetX($x);
        $p->SetFont('Arial', '', 9);
        $p->Cell($contentW, 4.8, $this->e('Señores'), 0, 1, 'L');
        $p->SetX($x);
        $p->SetFont('Arial', 'B', 9);
        $p->Cell($contentW, 4.8, $this->e('POLLO FIESTA S.A.'), 0, 1, 'L');
        $p->SetX($x);
        $p->SetFont('Arial', '', 9);
        $p->Cell($contentW, 4.8, $this->e($ciudad ?: 'Ciudad'), 0, 1, 'L');
        $p->Ln(5);

        $repLine = $rep ?: '______________________________';
        $empLine = $empresa ?: '________';
        $p->SetX($x);
        $p->SetFont('Arial', '', 9);
        $p->Cell($contentW, 5, $this->e("Yo {$repLine} en calidad de Representante Legal o propietario de {$empLine}"), 0, 1, 'L');
        $p->SetX($x);
        $p->Cell($contentW, 5, $this->e($nit ? ('NIT ' . $nit) : '_______________________________________________'), 0, 1, 'L');
        $p->Ln(2);

        $p->SetX($x);
        $p->SetFont('Arial', 'B', 9);
        $p->Cell($contentW, 5, $this->e('DECLARO QUE:'), 0, 1, 'L');
        $p->Ln(1);

        $textoOrigen = $origen ?: '________________';
        $p->SetX($x);
        $p->SetFont('Arial', '', 9);
        $p->MultiCell($contentW, 5, $this->e(
            'Mis propios recursos y /o los recursos de propiedad de la Sociedad que represento, provienen de actividades'
        ), 0, 'L');
        $p->SetX($x);
        $p->MultiCell($contentW, 5, $this->e(
            'lícitas y se originan de (Actividad que genera el vínculo comercial) ' . $textoOrigen
        ), 0, 'L');
        if (trim((string)$origen) === '') {
            $p->SetX($x);
            $p->Cell($contentW, 4, $this->e('____________________________________________________________________________________'), 0, 1, 'L');
            $p->SetX($x);
            $p->Cell($contentW, 4, $this->e('_____________________________________________________'), 0, 1, 'L');
        }

        $p->SetX($x);
        $p->SetFont('Arial', '', 9);
        $p->MultiCell($contentW, 5, $this->e(
            'Hemos tomado las medidas necesarias para no establecer ningún tipo de relación comercial con terceros'
        ), 0, 'L');
        $p->SetX($x);
        $p->MultiCell($contentW, 5, $this->e(
            'relacionados con actividades ilícitas, tipificadas en el Código Penal Colombiano.'
        ), 0, 'L');
        $p->Ln(3.2);

        $p->SetX($x);
        $p->SetFont('Arial', 'B', 9);
        $p->Cell($contentW, 5, $this->e('CALIDAD DE PEP'), 0, 1, 'L');
        $p->Ln(1.4);

        $pepNo = $esPep === 'no' ? 'X' : '';
        $pepSi = $esPep === 'si' ? 'X' : '';
        $famNo = $familiarPep === 'no' ? 'X' : '';
        $famSi = $familiarPep === 'si' ? 'X' : '';

        $p->SetX($x + 4);
        $p->SetFont('Arial', '', 8.4);
        $p->Cell(8, 5.4, 'NO', 0, 0, 'L');
        $p->Cell(6, 5.4, $this->e($pepNo ?: ' '), 1, 0, 'C');
        $p->Cell(3, 5.4, '', 0, 0, 'L');
        $p->Cell(7, 5.4, 'SI', 0, 0, 'L');
        $p->Cell(6, 5.4, $this->e($pepSi ?: ' '), 1, 0, 'C');
        $p->Cell(0, 5.4, $this->e('(Tachar lo que no corresponda) ostento la calidad de Persona Expuesta Políticamente, es decir no'), 0, 1, 'L');

        $p->SetX($x + 4);
        $p->Cell(68, 5.4, $this->e('administro recursos de origen público y así mismo'), 0, 0, 'L');
        $p->Cell(8, 5.4, 'NO', 0, 0, 'L');
        $p->Cell(6, 5.4, $this->e($famNo ?: ' '), 1, 0, 'C');
        $p->Cell(3, 5.4, '', 0, 0, 'L');
        $p->Cell(7, 5.4, 'SI', 0, 0, 'L');
        $p->Cell(6, 5.4, $this->e($famSi ?: ' '), 1, 0, 'C');
        $p->Cell(0, 5.4, $this->e('(Tachar lo que no corresponda) tengo relación'), 0, 1, 'L');

        $p->SetX($x + 16);
        $p->Cell($contentW - 16, 5.4, $this->e('parentesco con Persona Expuesta Políticamente.'), 0, 1, 'L');

        $detallePep = $this->v('cargo_pep') ?: $this->v('vinculo_pep_detalle') ?: '';
        $p->SetX($x);
        $p->SetFont('Arial', '', 9);
        $p->Cell($contentW, 5.4, $this->e('En caso afirmativo indicar:  Cargo/Función/Jerarquía o relación con la Persona Expuesta  Políticamente.'), 0, 1, 'L');
        if (trim($detallePep) !== '') {
            $p->SetX($x);
            $p->Cell($contentW, 4, $this->e($detallePep), 0, 1, 'L');
        } else {
            $p->SetX($x);
            $p->Cell($contentW, 4, $this->e('____________________________________________________________________________________'), 0, 1, 'L');
            $p->SetX($x);
            $p->Cell($contentW, 4, $this->e('_____________________________________________________'), 0, 1, 'L');
        }
        $p->Ln(4);

        $p->SetX($x);
        $p->SetFont('Arial', '', 9);
        $p->MultiCell($contentW, 5.2, $this->e(
            'Autorizamos para que esta información sea verificada, contrastada y analizada con las fuentes que POLLO'
        ), 0, 'L');
        $p->SetX($x);
        $p->MultiCell($contentW, 5.2, $this->e(
            'FIESTA S.A considere adecuadas para garantizar la efectividad de su Sistema de Anti-LA/FT.'
        ), 0, 'L');
        $p->Ln(10);

        $p->SetX($x);
        $p->SetFont('Arial', '', 9);
        $p->Cell($contentW, 5, $this->e('Atentamente,'), 0, 1, 'L');
        $p->Ln(9);
        $nombreFirma = $rep ?: $empresa ?: 'Representante Legal';
        $p->SetX($x);
        $p->Cell($contentW, 5, $this->e($nombreFirma), 0, 1, 'L');

        // Firma en imagen si existe en el formulario, si no línea de firma
        $sigFields = ['firma_declarante_data', 'firma_representante_data', 'firma_data', 'signature_data'];
        $sigField = '';
        foreach ($sigFields as $field) {
            if (!empty($this->d[$field])) {
                $sigField = $field;
                break;
            }
        }

        if ($sigField !== '') {
            $ySig = $p->GetY();
            $this->sigImage($sigField, $x, $ySig - 11, 55, 14);
            $p->Ln(2);
        } else {
            $p->SetX($x);
            $p->Cell($contentW, 5, $this->e('_________________________________'), 0, 1, 'L');
        }

        $p->SetX($x);
        $p->Cell($contentW, 5, $this->e('Nombre y Firma del Representante Legal y Sello'), 0, 1, 'L');
    }

    // =========================================================================
    // FCO-03  (Declaracion Origen Fondos - Proveedores)
    // =========================================================================
    private function fco03(): void
    {
        $p = $this->pdf;
        $W = 200; $h = 5.5; $hs = 5.0;

        $this->header('CONOCIMIENTO DE PROVEEDOR', 'DECLARACION ORIGEN DE FONDOS - PROVEEDORES', '01/01/18', '10/12/2025', '02', 'FCO-03');

        $this->sectionTitle('DECLARACION DE ORIGEN DE FONDOS', $W, $hs);
        $p->SetFont('Arial', '', 6);
        $p->MultiCell($W, 4.5, $this->e(
            'Yo, ' . $this->v('nombre_declarante') .
            ', identificado(a) con ' . $this->v('tipo_documento') .
            ' No. ' . $this->v('numero_documento') .
            ', en mi calidad de ' . $this->v('calidad') .
            ' de ' . ($this->v('company_name') ?: $this->v('empresa')) .
            ', identificada con NIT ' . ($this->v('nit') ?: $this->v('nit_empresa')) .
            ', DECLARO BAJO LA GRAVEDAD DE JURAMENTO que los recursos que utilizo en mis actividades comerciales provienen de actividades licitas y NO de ninguna actividad ilicita contemplada en el Codigo Penal Colombiano. Me comprometo a actualizar esta informacion anualmente o cuando se presenten cambios significativos.'
        ), 1, 'L');

        $this->sectionTitle('ORIGEN DE LOS RECURSOS', $W, $hs);
        $obsText = $this->e($this->v('origen_recursos'));
        $yObs = $p->GetY();
        $p->SetXY(5 + 36, $yObs);
        $p->SetFont('Arial', '', 6.5);
        $p->MultiCell($W - 36, 5, $obsText, 1, 'L');
        $yObsEnd = $p->GetY();
        $obsH = $yObsEnd - $yObs;
        $p->SetXY(5, $yObs);
        $p->SetFont('Arial', 'B', 6);
        $p->Cell(36, $obsH, $this->e('ORIGEN RECURSOS:'), 1, 0, 'L');
        $p->SetY($yObsEnd);

        $this->sectionTitle('CALIDAD DE PERSONA EXPUESTA POLITICAMENTE (PEP)', $W, $hs);
        $this->labelCell('ES USTED PEP?', 40, $h);
        $p->SetFont('Arial', '', 6);
        $p->Cell(8, $h, $this->v('es_pep') === 'si' ? 'X' : '', 1, 0, 'C');
        $p->Cell(16, $h, 'SI', 0, 0, 'L');
        $p->Cell(8, $h, $this->v('es_pep') === 'no' ? 'X' : '', 1, 0, 'C');
        $p->Cell(0, $h, 'NO', 0, 1, 'L');
        $this->row1('CARGO PEP:', $this->v('cargo_pep'), 24, $W - 24, $h);
        $this->labelCell('FAMILIAR PEP?', 40, $h);
        $p->SetFont('Arial', '', 6);
        $p->Cell(8, $h, $this->v('familiar_pep') === 'si' ? 'X' : '', 1, 0, 'C');
        $p->Cell(16, $h, 'SI', 0, 0, 'L');
        $p->Cell(8, $h, $this->v('familiar_pep') === 'no' ? 'X' : '', 1, 0, 'C');
        $p->Cell(0, $h, 'NO', 0, 1, 'L');
        $this->row1('FAMILIAR PEP DETALLE:', $this->v('familiar_pep_detalle'), 40, $W - 40, $h);

        $this->sectionTitle('INFORMACION ADICIONAL', $W, $hs);
        $this->row2('INGRESOS MENSUALES $', $this->money('ingresos_mensuales'), 40, 60, 'EGRESOS MENSUALES $', $this->money('egresos_mensuales'), 40, 60, $h);
        $this->row2('TOTAL ACTIVOS $', $this->money('total_activos'), 30, 70, 'TOTAL PASIVOS $', $this->money('total_pasivos'), 30, 70, $h);

        $p->SetFont('Arial', '', 5.2);
        $p->MultiCell($W, 3.8, $this->e('AVISO DE PRIVACIDAD: Autorizo a Pollo Fiesta S.A. (NIT 860.032.450-9) para tratamiento de datos segun Ley 1581/2012 y Decreto 1377/2013. Politica en www.pollo-fiesta.com'), 1, 'L');

        $this->sectionTitle('DECLARACION Y FIRMA', $W, $hs);
        $this->row1('NOMBRE COMPLETO:', $this->v('nombre_firma_final') ?: $this->v('nombre_declarante'), 34, $W - 34, $h);
        $this->row1('DOCUMENTO:', $this->v('documento_firma') ?: $this->v('numero_documento'), 24, $W - 24, $h);
        $this->row2('FECHA:', $this->v('fecha_declaracion'), 16, 84, 'CIUDAD:', $this->v('ciudad_declaracion'), 16, 84, $h);
        $this->row1('FIRMA DECLARANTE:', '', 34, $W - 34, 16);

        $this->sectionTitle('ESPACIO EXCLUSIVO PARA POLLO FIESTA', $W, $hs);
        $this->row1('VERIFICADO POR:', $this->v('verificado_por'), 30, $W - 30, $h);
        $this->row1('FECHA VERIFICACION:', $this->v('fecha_verificacion'), 36, $W - 36, $h);

        // FIRMA OFICIAL — mismo alto
        $firmaH = 16;
        $yFirma = $p->GetY();
        $p->SetXY(5 + 58, $yFirma);
        $p->Cell($W - 58, $firmaH, '', 1, 1, 'L');
        $this->sigImage('firma_oficial_data', 5 + 58 + 2, $yFirma + 1, 50, $firmaH - 2);
        $p->SetXY(5, $yFirma);
        $p->SetFont('Arial', 'B', 6);
        $p->Cell(58, $firmaH, $this->e('FIRMA DEL OFICIAL DE CUMPLIMIENTO:'), 1, 0, 'L');
        $p->SetY($yFirma + $firmaH);

        // OBSERVACIONES
        $obsText2 = $this->e($this->v('observaciones'));
        $yObs2 = $p->GetY();
        $p->SetXY(5 + 28, $yObs2);
        $p->SetFont('Arial', '', 6.5);
        $p->MultiCell($W - 28, 5, $obsText2, 1, 'L');
        $yObsEnd2 = $p->GetY();
        $obsH2 = $yObsEnd2 - $yObs2;
        $p->SetXY(5, $yObs2);
        $p->SetFont('Arial', 'B', 6);
        $p->Cell(28, $obsH2, $this->e('OBSERVACIONES:'), 1, 0, 'L');
        $p->SetY($yObsEnd2);
    }

    // =========================================================================
    // Helpers
    // =========================================================================
    private function header(string $title, string $subtitle, string $fe, string $fa, string $ver, string $cod): void
    {
        $p = $this->pdf;
        $W = 200;
        $logoPath = __DIR__ . '/../../public/assets/img/logo-pollo-fiesta.png';
        if (!file_exists($logoPath)) $logoPath = __DIR__ . '/../../public/assets/img/orb-logo.png';
        $logoW = 22; $logoH = 16;
        $y0 = $p->GetY();
        $p->Rect(5, $y0, $logoW, $logoH);
        if (file_exists($logoPath)) {
            $p->Image($logoPath, 5.5, $y0 + 1, $logoW - 1, $logoH - 2);
        } else {
            $p->SetXY(5, $y0 + 4);
            $p->SetFont('Arial', 'B', 5);
            $p->Cell($logoW, 5, 'POLLO FIESTA', 0, 0, 'C');
        }
        $p->SetXY(5 + $logoW, $y0);
        $p->SetFont('Arial', 'B', 10);
        $p->Cell($W - $logoW, 8, $this->e($title), 1, 1, 'C');
        $p->SetXY(5 + $logoW, $p->GetY());
        $p->SetFont('Arial', 'B', 8);
        $p->Cell($W - $logoW, 8, $this->e($subtitle), 1, 1, 'C');
        if ($p->GetY() < $y0 + $logoH) $p->SetY($y0 + $logoH);
        $p->SetFont('Arial', '', 6.5);
        $p->Cell(40, 5, $this->e('Fecha de emision: ' . $fe), 1, 0, 'L');
        $p->Cell(50, 5, $this->e('Fecha de actualizacion: ' . $fa), 1, 0, 'L');
        $p->Cell(20, 5, $this->e('Version: ' . $ver), 1, 0, 'C');
        $p->Cell(40, 5, $this->e('Codigo: ' . $cod), 1, 0, 'C');
        $p->Cell(50, 5, $this->e('Pagina 1 de 1'), 1, 1, 'C');
    }

    private function sectionTitle(string $title, float $w, float $h = 5.0): void
    {
        $this->pdf->SetFont('Arial', 'B', 6.5);
        $this->pdf->SetFillColor(220, 220, 220);
        $this->pdf->Cell($w, $h, $this->e($title), 1, 1, 'C', true);
        $this->pdf->SetFillColor(255, 255, 255);
    }

    private function labelCell(string $text, float $w, float $h = 5.5): void
    {
        $this->pdf->SetFont('Arial', 'B', 6);
        $this->pdf->Cell($w, $h, $this->e($text), 1, 0, 'L');
    }

    private function valueCell(string $value, float $w, float $h = 5.5): void
    {
        $this->pdf->SetFont('Arial', '', 6.5);
        $this->pdf->Cell($w, $h, $this->e($value), 1, 0, 'L');
    }

    private function centeredLabelCell(string $text, float $w, float $h = 5.5): void
    {
        $this->pdf->SetFont('Arial', 'B', 6.5);
        $this->pdf->Cell($w, $h, $this->e($text), 1, 0, 'C');
    }

    private function row1(string $l, string $v, float $lw, float $vw, float $h = 5.5): void
    {
        $this->labelCell($l, $lw, $h); $this->valueCell($v, $vw, $h); $this->pdf->Ln();
    }

    private function row2(string $l1, string $v1, float $lw1, float $vw1, string $l2, string $v2, float $lw2, float $vw2, float $h = 5.5): void
    {
        $this->labelCell($l1, $lw1, $h); $this->valueCell($v1, $vw1, $h);
        $this->labelCell($l2, $lw2, $h); $this->valueCell($v2, $vw2, $h);
        $this->pdf->Ln();
    }

    private function row2c(string $l1, float $w1, string $l2, float $w2, float $h = 5.5): void
    {
        $this->centeredLabelCell($l1, $w1, $h); $this->centeredLabelCell($l2, $w2, $h); $this->pdf->Ln();
    }

    private function row3(string $l1, string $v1, float $lw1, float $vw1, string $l2, string $v2, float $lw2, float $vw2, string $l3, string $v3, float $lw3, float $vw3, float $h = 5.5): void
    {
        $this->labelCell($l1, $lw1, $h); $this->valueCell($v1, $vw1, $h);
        $this->labelCell($l2, $lw2, $h); $this->valueCell($v2, $vw2, $h);
        $this->labelCell($l3, $lw3, $h); $this->valueCell($v3, $vw3, $h);
        $this->pdf->Ln();
    }

    private function sigImage(string $field, float $x, float $y, float $w, float $h): void
    {
        $uri = $this->d[$field] ?? '';
        if (empty($uri) || strpos($uri, 'data:image') !== 0) return;
        if (!preg_match('/^data:image\/(\w+);base64,(.+)$/', $uri, $m)) return;
        $ext = strtolower($m[1]);
        $data = base64_decode($m[2]);
        if (!$data) return;
        $tmp = tempnam(sys_get_temp_dir(), 'sig_') . '.' . $ext;
        file_put_contents($tmp, $data);
        try { $this->pdf->Image($tmp, $x, $y, $w, $h); } catch (\Exception $e) {}
        @unlink($tmp);
    }

    private function resolveKey(string $u, string $p, bool $intl, array $formData = []): string
    {
        $ft = $formData['form_type'] ?? '';
        // Mapeo directo desde form_type almacenado
        if ($ft === 'proveedor_internacional') return 'proveedor_internacional';
        if ($ft === 'declaracion_fondos_clientes')    return 'declaracion_cliente';
        if ($ft === 'declaracion_fondos_proveedores') return 'declaracion_proveedor';
        if ($ft === 'declaracion_cliente')   return 'declaracion_cliente';
        if ($ft === 'declaracion_proveedor') return 'declaracion_proveedor';
        if ($u === 'proveedor' && $intl) return 'proveedor_internacional';
        if ($p === 'declaracion') {
            return $u === 'proveedor' ? 'declaracion_proveedor' : 'declaracion_cliente';
        }
        return "{$u}_{$p}";
    }

    private function v(string $k): string
    {
        $aliases = [
            'address'            => ['direccion'],
            'nit'                => ['cc', 'numero_documento'],
            'company_name'       => ['nombre_cliente', 'nombre_proveedor', 'razon_social', 'empresa'],
            'nit_empresa'        => ['nit'],
            'telefono_fijo'      => ['telefono', 'phone'],
            'celular'            => ['phone'],
            'email'              => ['correo'],
            'origen_recursos'    => ['origen_fondos'],
            'nombre_declarante'  => ['representante_nombre', 'company_name'],
            'tipo_documento'     => ['representante_tipo_doc'],
            'numero_documento'   => ['representante_documento', 'nit'],
            'calidad'            => ['representante_profesion'],
            'empresa'            => ['company_name'],
            'es_pep'             => ['es_pep'],
            'cargo_pep'          => ['cargo_pep'],
            'periodo_pep'        => ['fecha_vinculacion_pep'],
            'familiar_pep'       => ['familiares_pep'],
            'familiar_pep_detalle' => ['familiares_pep'],
            'vinculo_pep'        => ['relacion_pep'],
            'vinculo_pep_detalle' => ['relacion_pep'],
            'opera_moneda_extranjera' => ['tiene_cuentas_exterior'],
            'paises_operacion'   => ['pais_cuentas_exterior'],
        ];
        $v = $this->d[$k] ?? '';
        if ($v === '' && isset($aliases[$k])) {
            foreach ($aliases[$k] as $a) { if (!empty($this->d[$a])) { $v = $this->d[$a]; break; } }
        }
        return (string)$v;
    }

    private function vRelated(string $k): string
    {
        if ($this->related === null) return '';
        $aliases = [
            'address'            => ['direccion'],
            'nit'                => ['cc', 'numero_documento'],
            'company_name'       => ['nombre_cliente', 'nombre_proveedor', 'razon_social', 'empresa'],
            'nit_empresa'        => ['nit'],
            'telefono_fijo'      => ['telefono', 'phone'],
            'celular'            => ['phone'],
            'email'              => ['correo'],
            'representante_nombre' => ['nombre_cliente'],
        ];
        $v = $this->related[$k] ?? '';
        if ($v === '' && isset($aliases[$k])) {
            foreach ($aliases[$k] as $a) { if (!empty($this->related[$a])) { $v = $this->related[$a]; break; } }
        }
        return (string)$v;
    }

    private function money(string $k): string
    {
        $v = $this->d[$k] ?? '';
        if ($v === '' || $v === null) return '';
        return '$ ' . number_format((float)$v, 0, ',', '.');
    }

    private function moneyRelated(string $k): string
    {
        if ($this->related === null) return '';
        $v = $this->related[$k] ?? '';
        if ($v === '' || $v === null) return '';
        return '$ ' . number_format((float)$v, 0, ',', '.');
    }

    /**
     * Normaliza respuestas tipo sí/no.
     * Retorna 'si', 'no' o ''.
     */
    private function yn(string $v): string
    {
        $v = trim(mb_strtolower((string)$v));
        if ($v === '') return '';
        $yes = ['si', 'sí', 's', 'yes', 'y', '1', 'true'];
        $no  = ['no', 'n', '0', 'false'];
        if (in_array($v, $yes, true)) return 'si';
        if (in_array($v, $no, true)) return 'no';
        return '';
    }

    /**
     * Checkbox visual consistente (cuadro + X).
     */
    private function cb(float $x, float $y, float $size, bool $checked): void
    {
        $p = $this->pdf;
        $p->Rect($x, $y, $size, $size);
        if (!$checked) return;
        $pad = 0.7;
        $p->Line($x + $pad, $y + $pad, $x + $size - $pad, $y + $size - $pad);
        $p->Line($x + $pad, $y + $size - $pad, $x + $size - $pad, $y + $pad);
    }

    private function e(string $s): string
    {
        return mb_convert_encoding($s, 'ISO-8859-1', 'UTF-8');
    }

    private function pdfMetaTitle(string $key, array $formData): string
    {
        $map = [
            'cliente_natural' => 'FGF-08',
            'cliente_juridica' => 'FGF-16',
            'declaracion_cliente' => 'FGF-17',
            'proveedor_natural' => 'FCO-05',
            'proveedor_juridica' => 'FCO-02',
            'proveedor_internacional' => 'FCO-04',
            'declaracion_proveedor' => 'FCO-03',
        ];
        $code = $map[$key] ?? 'SAGRILAFT';
        $id = (string)($formData['id'] ?? '');
        $company = trim((string)($formData['company_name'] ?? $formData['empresa'] ?? ''));
        if ($company === '') {
            return trim($code . ' Formulario SAGRILAFT' . ($id !== '' ? (' #' . $id) : ''));
        }
        return trim($code . ' - ' . $company . ($id !== '' ? (' (#' . $id . ')') : ''));
    }
}