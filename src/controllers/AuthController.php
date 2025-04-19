<?php

namespace App\controllers;

use App\config\Database;
use App\config\JwtHandler;
use App\models\User;

class AuthController
{
    private $db;
    public function __construct()
    {
        $this->db = (new Database())->getConnection();
    }

    public function register(): void
    {
        $u = trim($_POST['username'] ?? '');
        $e = trim($_POST['email']    ?? '');
        $p = $_POST['password']      ?? '';

        if (!$u || !$e || !$p) {
            header('Location: /register?error=Faltan+datos');
            exit;
        }

        $m = new User($this->db);
        if ($m->exists($u, $e)) {
            header('Location: /register?error=Usuario+o+email+ya+existe');
            exit;
        }

        $m->username = $u;
        $m->email    = $e;
        $m->setPassword($p);
        if ($m->create()) {
            header('Location: /login?success=Cuenta+creada');
            exit;
        }
        header('Location: /register?error=Error+interno');
    }

    public function login(): void
    {
        $u = trim($_POST['username'] ?? '');
        $p = $_POST['password']      ?? '';

        if (!$u || !$p) {
            header('Location: /login?error=Faltan+datos');
            exit;
        }

        $m = new User($this->db);
        if (!$m->readByUsername($u) || !password_verify($p, $m->password_hash)) {
            header('Location: /login?error=Credenciales+inválidas');
            exit;
        }

        $jwt = (new JwtHandler())->generateToken($m->user_id);
        // guardamos token en cookie segura
        setcookie('token', $jwt, [
            'httponly' => true,
            'samesite' => 'Lax',
            // 'secure' => true en producción HTTPS
        ]);

        header('Location: /products');
    }

    public function logout(): void
    {
        // 1) Expiramos la cookie:
        setcookie('token', '', [
            'expires'  => time() - 3600,   // hace que el navegador la borre
            'httponly' => true,
            'samesite' => 'Lax',
            'path'     => '/',             // importante: misma ruta que al crearla
        ]);
        // 2) Rediriges al formulario de login
        header('Location: /login?success=Sesión+cerrada');
        exit;
    }
}
