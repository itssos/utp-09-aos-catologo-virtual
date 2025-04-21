<?php
namespace App\Config;

use PDO;

class Database {
    /** @var PDO|null */
    private static $conn = null;

    /**
     * Retorna la conexión PDO singleton.
     */
    public static function getConnection(): PDO {
        if (self::$conn === null) {
            $host = DB_HOST;
            $db   = DB_NAME;
            $user = DB_USER;
            $pass = DB_PASS;
            $dsn  = "mysql:host={$host};dbname={$db};charset=utf8mb4";

            self::$conn = new PDO($dsn, $user, $pass, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ]);
        }
        return self::$conn;
    }
}
