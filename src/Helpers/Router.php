<?php

namespace App\Helpers;

use Exception;
use App\Middleware\SecurityMiddleware;

class Router
{
    private static array $routes = [];
    private static array $middlewares = [];
    private static ?string $prefix = null;

    /**
     * @param string $path
     * @param array|callable $handler
     * @param array $middlewares
     */
    public static function get(string $path, $handler, array $middlewares = []): void
    {
        self::addRoute('GET', $path, $handler, $middlewares);
    }

    /**
     * @param string $path
     * @param array|callable $handler
     * @param array $middlewares
     */
    public static function post(string $path, $handler, array $middlewares = []): void
    {
        self::addRoute('POST', $path, $handler, $middlewares);
    }

    /**
     * @param string $path
     * @param array|callable $handler
     * @param array $middlewares
     */
    public static function put(string $path, $handler, array $middlewares = []): void
    {
        self::addRoute('PUT', $path, $handler, $middlewares);
    }

    /**
     * @param string $path
     * @param array|callable $handler
     * @param array $middlewares
     */
    public static function patch(string $path, $handler, array $middlewares = []): void
    {
        self::addRoute('PATCH', $path, $handler, $middlewares);
    }

    /**
     * @param string $path
     * @param array|callable $handler
     * @param array $middlewares
     */
    public static function delete(string $path, $handler, array $middlewares = []): void
    {
        self::addRoute('DELETE', $path, $handler, $middlewares);
    }

    public static function group(string $prefix, callable $callback): void
    {
        $previousPrefix = self::$prefix;
        self::$prefix = $previousPrefix ? $previousPrefix . $prefix : $prefix;
        $callback();
        self::$prefix = $previousPrefix;
    }

    /**
     * @param string|array $middleware
     * @param callable $callback
     */
    public static function middleware($middleware, callable $callback): void
    {
        $previousMiddlewares = self::$middlewares;
        self::$middlewares = array_merge(
            self::$middlewares,
            is_array($middleware) ? $middleware : [$middleware]
        );
        $callback();
        self::$middlewares = $previousMiddlewares;
    }

    /**
     * @param string $method
     * @param string $path
     * @param array|callable $handler
     * @param array $middlewares
     */
    private static function addRoute(string $method, string $path, $handler, array $middlewares = []): void
    {
        $path = self::$prefix ? self::$prefix . $path : $path;
        self::$routes[] = [
            'method' => $method,
            'path' => $path,
            'handler' => $handler,
            'middlewares' => array_merge(self::$middlewares, $middlewares)
        ];
    }

    public static function dispatch(): void
    {
        try {
            $securityMiddleware = new SecurityMiddleware();
            $securityMiddleware->handle();

            $method = $_SERVER['REQUEST_METHOD'];
            $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
            $params = [];

            foreach (self::$routes as $route) {
                $pattern = preg_replace('/\{([^}]+)\}/', '(?P<\\1>[^/]+)', $route['path']);
                $pattern = '#^' . $pattern . '$#';

                if ($route['method'] === $method && preg_match($pattern, $path, $matches)) {
                    foreach ($matches as $key => $value) {
                        if (is_string($key)) {
                            $params[$key] = $value;
                        }
                    }

                    foreach ($route['middlewares'] as $middleware) {
                        $middlewareClass = "App\\Middleware\\$middleware";
                        $middlewareInstance = new $middlewareClass();
                        $middlewareInstance->handle();
                    }

                    // Get request body for POST, PUT, PATCH methods
                    $data = [];
                    if (in_array($method, ['POST', 'PUT', 'PATCH'])) {
                        $input = file_get_contents('php://input');
                        $data = json_decode($input, true) ?? [];
                    }

                    if (is_callable($route['handler'])) {
                        call_user_func($route['handler'], $params, $data);
                    } else {
                        [$controller, $method] = $route['handler'];
                        $controllerInstance = new $controller();
                        if (in_array($method, ['store', 'update'])) {
                            call_user_func([$controllerInstance, $method], $params, $data);
                        } else {
                            call_user_func([$controllerInstance, $method], $params);
                        }
                    }
                    return;
                }
            }

            throw new Exception('Route not found', 404);
        } catch (Exception $e) {
            $code = $e->getCode() ?: 500;
            http_response_code($code);
            echo json_encode([
                'status' => 'error',
                'message' => $e->getMessage(),
                'code' => $code
            ]);
        }
    }
}