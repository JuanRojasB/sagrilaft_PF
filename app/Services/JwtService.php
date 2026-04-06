<?php

namespace App\Services;

/**
 * Servicio JWT - Implementación en PHP Puro (Sin dependencias)
 * 
 * Genera y valida tokens JWT usando el algoritmo HS256.
 * No requiere librerías externas, solo PHP nativo.
 * 
 * Funciones:
 * - generate(): Crear token JWT con payload
 * - validate(): Validar y decodificar token JWT
 * 
 * Estructura del token: header.payload.signature
 * 
 * @package App\Services
 */
class JwtService
{
    private string $secret;
    private int $expiration;

    public function __construct()
    {
        $this->secret = $_ENV['JWT_SECRET'];
        $this->expiration = (int)$_ENV['JWT_EXPIRATION'];
    }

    /**
     * Generar token JWT
     * 
     * Crea un token JWT con el payload proporcionado.
     * Incluye fecha de emisión (iat) y expiración (exp).
     * 
     * @param array $payload Datos del usuario
     * @return string Token JWT
     */
    public function generate(array $payload): string
    {
        $header = $this->base64UrlEncode(json_encode([
            'alg' => 'HS256',
            'typ' => 'JWT'
        ]));

        $payload['iat'] = time();
        $payload['exp'] = time() + $this->expiration;
        
        $body = $this->base64UrlEncode(json_encode($payload));
        
        $signature = $this->base64UrlEncode(
            hash_hmac('sha256', "$header.$body", $this->secret, true)
        );

        return "$header.$body.$signature";
    }

    /**
     * Validar y decodificar token JWT
     * 
     * Verifica la firma y la expiración del token.
     * 
     * @param string $token Token JWT
     * @return array|null Datos decodificados o null si es inválido
     */
    public function validate(string $token): ?array
    {
        $parts = explode('.', $token);
        
        if (count($parts) !== 3) {
            return null;
        }

        [$header, $body, $signature] = $parts;

        // Verify signature
        $validSignature = $this->base64UrlEncode(
            hash_hmac('sha256', "$header.$body", $this->secret, true)
        );

        if (!hash_equals($validSignature, $signature)) {
            return null;
        }

        // Decode payload
        $payload = json_decode($this->base64UrlDecode($body), true);

        // Check expiration
        if (!isset($payload['exp']) || $payload['exp'] < time()) {
            return null;
        }

        return $payload;
    }

    /**
     * Codificar en Base64 URL
     * 
     * Codifica datos en Base64 compatible con URLs.
     */
    private function base64UrlEncode(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    /**
     * Decodificar Base64 URL
     * 
     * Decodifica datos en Base64 compatible con URLs.
     */
    private function base64UrlDecode(string $data): string
    {
        return base64_decode(strtr($data, '-_', '+/'));
    }
}
