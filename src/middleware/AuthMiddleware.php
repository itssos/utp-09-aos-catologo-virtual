<?php

namespace App\Middleware;

use App\config\JwtHandler;
use App\Models\User;

class AuthMiddleware
{
  public function handle(): bool
  {
    if (empty($_COOKIE['token'])) {
      header('Location: /admin/login?error=Debe+loguearse');
      return false;
    }
    $decoded = (new JwtHandler())->validateToken($_COOKIE['token']);
    if (!$decoded) {
      setcookie('token', '', time() - 3600);
      header('Location: /admin/login?error=Token+inválido');
      return false;
    }
    // opcional: $userId = $decoded->sub;
    return true;
  }

  /**
   * @return User|null  Instancia de User o null si no hay JWT válido.
   */
  public static function getUser(): ?User
  {
    $token = null;

    // 1) Intentar header Authorization: Bearer <token>
    if (
      !empty($_SERVER['HTTP_AUTHORIZATION'])
      && preg_match('/Bearer\s(\S+)/', $_SERVER['HTTP_AUTHORIZATION'], $m)
    ) {
      $token = $m[1];
    }
    // 2) O desde cookie 'token'
    elseif (!empty($_COOKIE['token'])) {
      $token = $_COOKIE['token'];
    }

    if (!$token) {
      return null;
    }

    $userId = JwtHandler::getUserId($token);
    if (!$userId) {
      return null;
    }

    return User::findById($userId);
  }
}
