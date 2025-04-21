<?php
require_once __DIR__ . '/constants.php';

use App\Models\User;
use App\Middleware\AuthMiddleware;

/**
 * Genera la URL completa de una ruta por clave.
 */
function route(string $name): string {
    if (!isset(ROUTES[$name])) {
        throw new Exception("Route “{$name}” no definida");
    }
    return BASE_URL . ROUTES[$name];
}

/**
 * Retorna la instancia de User si hay un JWT válido, o null.
 */
function currentUser(): ?User {
    return AuthMiddleware::getUser();
}

/**
 * ¿Hay un usuario autenticado?
 */
function isAuth(): bool {
    return currentUser() instanceof User;
}

/**
 * ¿El usuario autenticado tiene el permiso dado?
 * $permKey debe coincidir con una clave de PERMISSIONS.
 */
function can(string $permKey): bool {
    $user = currentUser();
    if (!$user) {
        return false;
    }
    if (!isset(PERMISSIONS[$permKey])) {
        throw new Exception("Permission key “{$permKey}” no definido");
    }
    return $user->hasPermission(PERMISSIONS[$permKey]);
}

// flash messages via cookies
function setFlash(string $type, string $msg): void {
    setcookie("flash_{$type}", $msg, 0, '/');
}

function getFlash(string $type): ?string {
    if (!empty($_COOKIE["flash_{$type}"])) {
        $m = $_COOKIE["flash_{$type}"];
        setcookie("flash_{$type}", '', time() - 3600, '/');
        return $m;
    }
    return null;
}