<?php
require_once __DIR__ . '/constants.php';

use App\Models\User;
use App\Middleware\AuthMiddleware;

/**
 * Genera la URL completa de una ruta por clave.
 */
function route(string $name): string
{
    if (!isset(ROUTES[$name])) {
        throw new Exception("Route “{$name}” no definida");
    }
    return BASE_URL . ROUTES[$name];
}

/**
 * Retorna la instancia de User si hay un JWT válido, o null.
 */
function currentUser($pdo): ?User
{
    return AuthMiddleware::getUser($pdo);
}

/**
 * ¿Hay un usuario autenticado?
 */
function isAuth($pdo): bool
{
    return currentUser($pdo) instanceof User;
}

/**
 * ¿El usuario autenticado tiene el permiso dado?
 * $permKey debe coincidir con una clave de PERMISSIONS.
 */
function can(string $permKey, $pdo): bool
{
    $user = currentUser($pdo);
    if (!$user) {
        return false;
    }
    // if (!isset(PERMISSIONS[$permKey])) {
    //     throw new Exception("Permission key “{$permKey}” no definido");
    // }
    return $user->hasPermission($permKey);
}

// flash messages via cookies
function setFlash(string $type, string $msg): void
{
    setcookie("flash_{$type}", $msg, 0, '/');
}

function getFlash(string $type): ?string
{
    if (!empty($_COOKIE["flash_{$type}"])) {
        $m = $_COOKIE["flash_{$type}"];
        setcookie("flash_{$type}", '', time() - 3600, '/');
        return $m;
    }
    return null;
}

function httpRequest(string $method, string $path, array $body = []): object
{
    $url = API_BASE_URL . $path;
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);   // timeout de conexión
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);          // timeout de respuesta
    // curl_setopt($ch, CURLOPT_HEADER, true);

    if (!empty($_SERVER['HTTP_COOKIE'])) {
        curl_setopt($ch, CURLOPT_COOKIE, $_SERVER['HTTP_COOKIE']);
    }

    $headers = ['Accept: application/json'];
    if (in_array($method, ['POST', 'PUT', 'DELETE'])) {
        $payload = json_encode($body);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        $headers[] = 'Content-Type: application/json';
    }
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    $response = curl_exec($ch);
    $err = curl_errno($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($err) {
        throw new Exception("Error en cURL: " . curl_strerror($err));
    }
    if ($httpCode >= 400) {
        throw new Exception("API devolvió HTTP {$httpCode} - {$method} {$url}");
    }

    $data = json_decode($response, true);
    if (!isset($data['status']) || $data['status'] !== 'success') {
        $msg = $data['message'] ?? 'Error en API';
        throw new Exception($msg);
    }
    return json_decode(json_encode($data));
}
