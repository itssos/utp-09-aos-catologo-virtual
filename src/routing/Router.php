<?php

declare(strict_types=1);

namespace App\Routing;

use PDO;

/**
 * Simple Router para definir rutas y callbacks.
 */
class Router
{
    private array $routes = [];

    /**
     * Registra una ruta HTTP para un método dado, opcionalmente con permiso.
     *
     * @param string $method GET|POST|PUT|DELETE
     * @param string $pathRuta Ruta, puede contener {param}
     * @param callable $handler Callback que recibe (array $params)
     * @param string|null $permission Permiso requerido (clave de PERMISSIONS)
     */
    public function add(string $method, string $pathRuta, callable $handler, ?string $permission = null): void
    {
        // Convertir "/api/{resource}/{id}" a regex
        $pattern = preg_replace('#\{(\w+)\}#', '(?P<$1>[^/]+)', $pathRuta);
        $pattern = '#^' . $pattern . '$#';
        $this->routes[] = [
            'method'     => strtoupper($method),
            'pattern'    => $pattern,
            'handler'    => $handler,
            'permission' => $permission,
        ];
    }

    public function get(string $path, callable $handler, ?string $permission = null): void { $this->add('GET', $path, $handler, $permission); }
    public function post(string $path, callable $handler, ?string $permission = null): void { $this->add('POST', $path, $handler, $permission); }
    public function put(string $path, callable $handler, ?string $permission = null): void { $this->add('PUT', $path, $handler, $permission); }
    public function delete(string $path, callable $handler, ?string $permission = null): void { $this->add('DELETE', $path, $handler, $permission); }

    /**
     * Despacha la petición actual al handler correspondiente.
     *
     * @param string $method
     * @param string $uri
     */
    public function dispatch(string $method, string $uri, PDO $pdo): void
    {
        foreach ($this->routes as $route) {
            if ($route['method'] !== strtoupper($method)) {
                continue;
            }
            if (preg_match($route['pattern'], $uri, $matches)) {
                // Verificar permisos si aplica
                if ($route['permission'] !== null && !\can($route['permission'], $pdo)) {
                    http_response_code(403);
                    echo json_encode(['status' => 'error', 'message' => 'Forbidden']);
                    return;
                }
                // Filtrar solo parámetros nombrados
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
                // Llamar handler
                call_user_func($route['handler'], $params);
                return;
            }
        }

        // Si no hay coincidencia
        http_response_code(404);
        echo json_encode(['status' => 'error', 'message' => 'Ruta no encontrada']);
    }
}