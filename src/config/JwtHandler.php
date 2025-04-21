<?php

namespace App\config;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Exception;

class JwtHandler {
    private static string $secret = JWT_SECRET;
    private string $issuer;
    private int $issuedAt;
    private int $notBefore;
    private int $expire;

    public function __construct() {
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
        return JWT::encode($payload, self::$secret, 'HS256');
    }

    /**
     * Valida y decodifica un JWT. Devuelve el payload o null.
     */
    public static function validateToken(string $jwt): ?object {
        try {
            return JWT::decode($jwt, new Key(self::$secret, 'HS256'));
        } catch (Exception $e) {
            return null;
        }
    }

    public static function getUserId(string $token): ?int {
        $decoded = self::validateToken($token);
        return $decoded->sub ?? null;
    }

}
