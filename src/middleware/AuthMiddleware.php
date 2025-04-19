<?php

namespace App\middleware;
use App\config\JwtHandler;

class AuthMiddleware {
  public function handle(): bool {
    if (empty($_COOKIE['token'])) {
      header('Location: /login?error=Debe+loguearse');
      return false;
    }
    $decoded = (new JwtHandler())->validateToken($_COOKIE['token']);
    if (!$decoded) {
      setcookie('token', '', time()-3600);
      header('Location: /login?error=Token+inválido');
      return false;
    }
    // opcional: $userId = $decoded->sub;
    return true;
  }
}
