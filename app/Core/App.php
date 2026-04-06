<?php

namespace App\Core;

use App\Services\Logger;

/**
 * Main Application Class
 * 
 * Clase principal de la aplicación que inicializa el router,
 * registra todas las rutas del sistema y maneja la ejecución.
 * 
 * @package App\Core
 */
class App
{
    private Router $router;
    private Logger $logger;

    /**
     * Constructor - Inicializa el router, logger y registra las rutas
     */
    public function __construct()
    {
        $this->router = new Router();
        $this->logger = new Logger();
        $this->registerRoutes();
    }

    /**
     * Registra todas las rutas de la aplicación
     * 
     * Define las rutas para:
     * - Autenticación (login/logout/registro)
     * - Formularios (CRUD)
     * - Aprobación de formularios (revisores)
     * - Administración de usuarios (solo admin)
     */
    private function registerRoutes(): void
    {
        // Rutas de autenticación - Login, logout (URL específica para admin/revisor)
        $this->router->get('/admin-access', 'AuthController@showLogin');
        $this->router->get('/login', 'AuthController@showLogin'); // Mantener por compatibilidad
        $this->router->post('/login', 'AuthController@login');
        $this->router->post('/logout', 'AuthController@logout');
        
        // Ruta principal - Formulario de registro simplificado
        $this->router->get('/', 'HomeController@index');
        $this->router->post('/home/register', 'HomeController@register');
        
        // Ruta directa al formulario (sin autenticación)
        $this->router->get('/form/create', 'FormController@createDirect');
        $this->router->post('/form/store', 'FormController@storeDirect');
        $this->router->post('/form/store-pdf', 'FormController@storePdf');
        $this->router->get('/form/success', 'FormController@success');
        
        // Rutas de declaración de origen de fondos (Paso 2)
        $this->router->get('/form/declaracion', 'FormController@showDeclaracion');
        $this->router->post('/form/declaracion/store', 'FormController@storeDeclaracion');
        
        // Rutas de recuperación de contraseña
        $this->router->get('/password/forgot', 'PasswordResetController@showForgotForm'); // Formulario solicitar código
        $this->router->post('/password/send-code', 'PasswordResetController@sendCode'); // Enviar código por email
        $this->router->get('/password/reset', 'PasswordResetController@showResetForm'); // Formulario restablecer
        $this->router->post('/password/reset', 'PasswordResetController@resetPassword'); // Procesar restablecimiento
        
        // Rutas de formularios - Requieren autenticación
        $this->router->get('/forms', 'FormController@index', ['auth']); // Lista de formularios
        $this->router->get('/forms/create', 'FormController@create', ['auth']); // Formulario de creación
        $this->router->post('/forms', 'FormController@store', ['auth']); // Guardar formulario
        $this->router->get('/forms/{id}', 'FormController@show', ['auth']); // Ver detalle
        $this->router->get('/forms/{id}/view', 'FormController@viewComplete'); // Ver formulario completo
        $this->router->get('/forms/{id}/pdf', 'FormController@generatePdf', ['auth']); // Generar PDF (autenticado)
        $this->router->get('/form/{id}/pdf', 'FormController@generatePdf'); // Generar PDF (público, post-envío)
        $this->router->post('/forms/{id}/pollo-fiesta', 'FormController@savePolloFiesta'); // Guardar campos internos
        $this->router->get('/forms/attachment/{id}', 'FormController@downloadAttachment', ['auth']); // Descargar adjunto (usuario autenticado)
        $this->router->get('/reviewer/attachment/{id}', 'FormController@downloadAttachmentReviewer'); // Descargar adjunto (revisor)
        $this->router->get('/reviewer/form/{id}/pdf', 'FormController@generatePdfReviewer'); // Ver PDF (revisor)
        $this->router->post('/forms/{id}/consolidate', 'FormController@consolidatePDFs', ['auth', 'role:revisor']); // Consolidar PDFs (solo revisor)
        $this->router->get('/forms/consolidated/{id}/download', 'FormController@downloadConsolidatedPDF', ['auth']); // Descargar PDF consolidado
        
        // Rutas de perfil - Requieren autenticación
        $this->router->get('/profile', 'ProfileController@edit', ['auth']); // Editar perfil
        $this->router->post('/profile/update', 'ProfileController@update', ['auth']); // Actualizar perfil
        $this->router->post('/profile/upload-documents', 'ProfileController@uploadDocuments', ['auth']); // Subir documentos
        $this->router->get('/profile/download/{type}', 'ProfileController@downloadDocument', ['auth']); // Descargar documento
        
        // Rutas de documentos - Requieren autenticación
        $this->router->get('/documentos', 'DocumentoController@misDocumentos', ['auth']); // Mis documentos
        $this->router->post('/documentos/upload', 'DocumentoController@upload', ['auth']); // Subir documento
        $this->router->get('/documentos/download/{id}', 'DocumentoController@download', ['auth']); // Descargar documento
        $this->router->post('/documentos/delete/{id}', 'DocumentoController@delete', ['auth']); // Eliminar documento
        
        // Rutas de revisor - Login y dashboard para revisores
        $this->router->get('/reviewer/login', 'ApprovalController@login');
        $this->router->post('/reviewer/login', 'ApprovalController@processLogin');
        $this->router->get('/reviewer/dashboard', 'ApprovalController@dashboard');
        $this->router->post('/reviewer/logout', 'ApprovalController@logout');
        
        // Rutas de evaluación de documentos - OBSOLETAS (comentadas)
        // $this->router->get('/reviewer/documentos', 'ReviewerController@documentos', ['auth', 'role:revisor']);
        // $this->router->get('/reviewer/documentos/usuario/{userId}', 'ReviewerController@verDocumentosUsuario', ['auth', 'role:revisor']);
        // $this->router->post('/reviewer/documentos/evaluar/{id}', 'ReviewerController@evaluarDocumento', ['auth', 'role:revisor']);
        // $this->router->post('/reviewer/documentos/consolidar/{userId}', 'ReviewerController@consolidarPDFs', ['auth', 'role:revisor']);
        // $this->router->post('/reviewer/documentos/firmar/{pdfId}', 'ReviewerController@firmarPDF', ['auth', 'role:revisor']);
        // $this->router->post('/reviewer/firma/upload', 'ReviewerController@uploadFirma', ['auth', 'role:revisor']);
        
        // Rutas de aprobación - Públicas (acceso por token en email)
        $this->router->get('/approval/{token}', 'ApprovalController@show'); // Ver formulario para aprobar
        $this->router->post('/approval/{token}', 'ApprovalController@process'); // Aprobar/rechazar
        
        // Rutas de administración - Solo para usuarios con role=admin
        $this->router->get('/admin', 'AdminController@index', ['auth', 'role:admin']); // Dashboard admin
        $this->router->get('/admin/users', 'AdminController@users', ['auth', 'role:admin']); // Gestión de usuarios
        $this->router->post('/admin/users/create', 'AdminController@createUser', ['auth', 'role:admin']); // Crear usuario
        $this->router->post('/admin/users/update/{id}', 'AdminController@updateUser', ['auth', 'role:admin']); // Actualizar usuario
        $this->router->post('/admin/users/delete/{id}', 'AdminController@deleteUser', ['auth', 'role:admin']); // Eliminar usuario
        $this->router->get('/admin/logistics', 'AdminController@logistics', ['auth', 'role:admin']); // Evaluación logística
        $this->router->post('/admin/logistics/evaluate', 'AdminController@evaluateLogistics', ['auth', 'role:admin']); // Evaluar logística
        $this->router->get('/admin/logistics/download/{userId}/{type}', 'AdminController@downloadUserDocument', ['auth', 'role:admin']); // Descargar documento
        $this->router->get('/admin/vendedores', 'AdminController@vendedores', ['auth', 'role:admin']); // Gestión de vendedores
        $this->router->post('/admin/vendedores/create', 'AdminController@createVendedor', ['auth', 'role:admin']); // Crear vendedor
        $this->router->post('/admin/vendedores/update/{id}', 'AdminController@updateVendedor', ['auth', 'role:admin']); // Actualizar vendedor
        $this->router->post('/admin/vendedores/delete/{id}', 'AdminController@deleteVendedor', ['auth', 'role:admin']); // Eliminar vendedor
        $this->router->post('/admin/vendedores/asignar', 'AdminController@asignarVendedor', ['auth', 'role:admin']); // Asignar vendedor
        
        // Rutas de sincronización Excel - Para admin y revisor
        $this->router->post('/excel/download-filtered', 'ExcelSyncController@downloadFiltered', ['auth']); // Descargar Excel filtrado desde dashboard
        
        // Ruta por defecto - Redirige a la página principal
        $this->router->get('/old-login', 'AuthController@showLogin');
    }

    /**
     * Ejecuta la aplicación
     * 
     * Despacha la ruta actual y maneja cualquier error que ocurra.
     * En desarrollo muestra el error completo, en producción muestra mensaje genérico.
     */
    public function run(): void
    {
        try {
            // Despachar la ruta actual
            $this->router->dispatch();
        } catch (\Exception $e) {
            // Registrar el error en los logs
            $this->logger->error('Application error: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Responder con error 500
            http_response_code(500);
            
            // Mostrar detalles solo en desarrollo
            if ($_ENV['APP_ENV'] === 'development') {
                echo '<pre>' . $e->getMessage() . '</pre>';
            } else {
                echo 'Error interno del servidor';
            }
        }
    }
}
