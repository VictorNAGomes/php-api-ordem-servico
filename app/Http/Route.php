<?php 

namespace App\Http;

class Route 
{
    private static array $routes = [];

    public static function get(string $path, string $action, array $middleware = [])
    {
        self::$routes[] = [
            'path'   => $path,
            'action' => $action,
            'method' => 'GET',
            'middleware' => $middleware
        ];
    }

    public static function post(string $path, string $action, array $middleware = [])
    {
        self::$routes[] = [
            'path'   => $path,
            'action' => $action,
            'method' => 'POST',
            'middleware' => $middleware
        ];
    }

    public static function put(string $path, string $action, array $middleware = [])
    {
        self::$routes[] = [
            'path'   => $path,
            'action' => $action,
            'method' => 'PUT',
            'middleware' => $middleware
        ];
    }

    public static function delete(string $path, string $action, array $middleware = [])
    {
        self::$routes[] = [
            'path'   => $path,
            'action' => $action,
            'method' => 'DELETE',
            'middleware' => $middleware
        ];
    }

    public static function routes()
    {
        return self::$routes;
    }
}