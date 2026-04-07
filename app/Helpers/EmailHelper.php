<?php

namespace App\Helpers;

/**
 * Helper para funciones relacionadas con emails
 */
class EmailHelper
{
    /**
     * Genera el header HTML común para todos los emails del sistema.
     * Incluye logo de Pollo Fiesta S.A. y título del email.
     */
    public static function emailHeader(string $title): string
    {
        // Logo referenciado por CID — se embebe como parte MIME en sendViaSMTPWithImages
        return "
        <!DOCTYPE html>
        <html lang='es'>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width,initial-scale=1.0'>
            <meta http-equiv='X-UA-Compatible' content='IE=edge'>
            <!--[if mso]><xml><o:OfficeDocumentSettings><o:AllowPNG/><o:PixelsPerInch>96</o:PixelsPerInch></o:OfficeDocumentSettings></xml><![endif]-->
            <title>{$title}</title>
            <style>
                *{margin:0;padding:0;box-sizing:border-box;}
                body{font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Arial,sans-serif;line-height:1.5;color:#e2e8f0;background:#020617;padding:20px;-webkit-text-size-adjust:100%;-ms-text-size-adjust:100%;}
                .wrapper{max-width:640px;margin:0 auto;background:#0f172a;border:1px solid #1e3a5f;border-radius:8px;overflow:hidden;}
                .top-bar{background:#0a1628;padding:14px 24px;border-bottom:2px solid #1d4ed8;}
                .top-bar-name{font-size:15px;font-weight:700;color:#e2e8f0;display:block;line-height:1.2;}
                .top-bar-sub{font-size:10px;color:#64748b;letter-spacing:0.6px;text-transform:uppercase;display:block;}
                .email-title{background:#0f172a;padding:18px 24px;border-bottom:1px solid #1e293b;}
                .email-title h1{font-size:16px;font-weight:600;color:#e2e8f0;margin:0;}
                .body{padding:24px;}
                .info-item{background:#1e293b;border:1px solid #334155;border-radius:6px;padding:10px 14px;margin-bottom:8px;}
                .info-label{font-size:10px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:0.6px;display:block;margin-bottom:3px;}
                .info-value{font-size:13px;color:#e2e8f0;}
                .badge{display:inline-block;padding:7px 16px;border-radius:4px;font-weight:700;font-size:12px;margin-bottom:16px;letter-spacing:0.5px;}
                .badge-green{background:rgba(34,197,94,0.15);color:#4ade80;border:1px solid rgba(34,197,94,0.35);}
                .badge-yellow{background:rgba(251,191,36,0.15);color:#fbbf24;border:1px solid rgba(251,191,36,0.35);}
                .badge-red{background:rgba(239,68,68,0.15);color:#f87171;border:1px solid rgba(239,68,68,0.35);}
                .badge-blue{background:rgba(59,130,246,0.15);color:#60a5fa;border:1px solid rgba(59,130,246,0.35);}
                .obs-box{background:rgba(251,191,36,0.08);border-left:3px solid #f59e0b;padding:12px 16px;border-radius:0 4px 4px 0;margin:16px 0;}
                .obs-box.red{background:rgba(239,68,68,0.08);border-left-color:#ef4444;}
                .obs-box strong{display:block;margin-bottom:6px;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.5px;color:#fbbf24;}
                .obs-box.red strong{color:#f87171;}
                .obs-box p{font-size:13px;color:#fde68a;line-height:1.5;}
                .obs-box.red p{color:#fecaca;}
                .msg{font-size:13px;color:#94a3b8;margin-bottom:16px;}
                .btn{display:inline-block;padding:10px 22px;background:#1d4ed8 !important;color:#ffffff !important;text-decoration:none !important;border-radius:5px;font-weight:600;font-size:13px;margin-top:8px;}
                .divider{height:1px;background:#1e293b;margin:20px 0;}
                @media only screen and (max-width:480px){
                    .body{padding:16px !important;}
                    .top-bar{padding:12px 16px !important;}
                    .email-title{padding:14px 16px !important;}
                }
            </style>
        </head>
        <body>
        <div class='wrapper'>
            <div class='top-bar'>
                <table role='presentation' cellspacing='0' cellpadding='0' border='0' width='100%'>
                    <tr>
                        <td style='padding-right:12px;width:44px;vertical-align:middle;'>
                            <img src='cid:logo' alt='Pollo Fiesta' width='36' height='36' style='display:block;width:36px;height:36px;'>
                        </td>
                        <td style='vertical-align:middle;'>
                            <span class='top-bar-name'>Pollo Fiesta S.A.</span>
                            <span class='top-bar-sub'>Sistema SAGRILAFT &nbsp;·&nbsp; NIT 860.032.450-9</span>
                        </td>
                    </tr>
                </table>
            </div>
            <div class='email-title'><h1>{$title}</h1></div>
            <div class='body'>";
    }

    /**
     * Genera el footer HTML común para todos los emails del sistema.
     * Incluye imagen de firma corporativa y pie de página.
     */
    public static function emailFooter(): string
    {
        $token = substr(md5(uniqid('', true)), 0, 8);
        return "
            </div>
            <div style='padding:16px 24px;background:#0a1628;border-top:1px solid #1e293b;text-align:center;'>
                " . self::getSignatureImage() . "
            </div>
            <div style='padding:12px 24px;background:#020617;text-align:center;border-top:1px solid #0f172a;'>
                <p style='font-size:11px;color:#475569;margin:2px 0;'><strong style='color:#334155;'>Pollo Fiesta S.A.</strong> &nbsp;·&nbsp; NIT 860.032.450-9</p>
                <p style='font-size:11px;color:#475569;margin:2px 0;'>Sistema SAGRILAFT &nbsp;·&nbsp; &copy; " . date('Y') . " Todos los derechos reservados</p>
                <p style='font-size:11px;color:#334155;margin-top:8px;'>Este es un mensaje automático, por favor no responder a este correo.</p>
                <!-- {$token} -->
            </div>
        </div>
        </body></html>";
    }

    /**
     * Convierte una imagen a base64 para embeber en correos
     * 
     * @param string $imagePath Ruta relativa desde la raíz del proyecto
     * @return string|null Data URI de la imagen o null si no existe
     */
    public static function imageToBase64(string $imagePath): ?string
    {
        $fullPath = __DIR__ . '/../../' . $imagePath;
        
        if (!file_exists($fullPath)) {
            return null;
        }
        
        $imageData = file_get_contents($fullPath);
        if ($imageData === false) {
            return null;
        }
        
        // Detectar tipo MIME
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $fullPath);
        finfo_close($finfo);
        
        // Convertir a base64
        $base64 = base64_encode($imageData);
        
        return "data:{$mimeType};base64,{$base64}";
    }
    
    /**
     * Obtiene la firma corporativa como imagen embebida
     * 
     * @return string HTML con la imagen usando CID
     */
    public static function getSignatureImage(): string
    {
        return "<img src='cid:signature' alt='SAGRILAFT' width='600' style='width:100%;max-width:600px;height:auto;display:block;margin:0 auto;' />";
    }

    /**
     * Obtiene la ruta completa del logo
     */
    public static function getLogoImagePath(): ?string
    {
        $fullPath = __DIR__ . '/../../public/assets/img/orb-logo-email.png';
        return file_exists($fullPath) ? $fullPath : null;
    }
    
    /**
     * Obtiene la ruta completa de la imagen de firma
     */
    public static function getSignatureImagePath(): ?string
    {
        $fullPath = __DIR__ . '/../../public/assets/img/correo_info_angie-email.jpg';
        if (file_exists($fullPath)) return $fullPath;
        // fallback al PNG original
        $fullPath = __DIR__ . '/../../public/assets/img/correo_info_angie.png';
        return file_exists($fullPath) ? $fullPath : null;
    }
    
    /**
     * Genera un enlace de Google Maps para una dirección
     * 
     * @param string $address Dirección a buscar
     * @param string $buttonText Texto del botón (opcional)
     * @return string HTML con el enlace a Google Maps
     */
    public static function getGoogleMapsLink(string $address, string $buttonText = '📍 Ver en Google Maps'): string
    {
        if (empty($address) || $address === 'N/A') {
            return '';
        }
        
        $encodedAddress = urlencode($address);
        $mapsUrl = "https://www.google.com/maps/search/?api=1&query={$encodedAddress}";
        
        return "
        <div style='margin-top: 8px;'>
            <a href='{$mapsUrl}' 
               target='_blank' 
               style='display: inline-block; padding: 6px 12px; background: #3b82f6; color: #ffffff; text-decoration: none; border-radius: 4px; font-size: 12px; font-weight: 500;'>
                {$buttonText}
            </a>
        </div>";
    }
}
