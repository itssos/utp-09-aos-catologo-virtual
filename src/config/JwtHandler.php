<?php

namespace App\config;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Exception;

class JwtHandler {
    private string $secret;
    private string $issuer;
    private int $issuedAt;
    private int $notBefore;
    private int $expire;

    public function __construct() {
        $this->secret   = JWT_SECRET;
        $this->issuer   = JWT_ISSUER;
        $this->issuedAt = time();
        $this->notBefore= $this->issuedAt;
        $this->expire   = $this->issuedAt + 3600; // 1 hora
    }

    /**
     * Genera un token JWT con el ID de usuario como “sub”.
     */
    public function generateToken(int $userId): string {
        $payload = [
            'iss'  => $this->issuer,
            'iat'  => $this->issuedAt,
            'nbf'  => $this->notBefore,
            'exp'  => $this->expire,
            'sub'  => $userId
        ];
        return JWT::encode($payload, $this->secret, 'HS256');
    }

    /**
     * Valida y decodifica un JWT. Devuelve el payload o null.
     */
    public function validateToken(string $jwt): ?object {
        try {
            return JWT::decode($jwt, new Key($this->secret, 'HS256'));
        } catch (Exception $e) {
            return null;
        }
    }
}
