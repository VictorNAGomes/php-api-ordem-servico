<?php 

namespace App\Core;

use App\Http\Request;
use App\Http\Response;
use App\Core\Middleware;

class Core 
{
    public static function dispatch(array $routes)
    {
        $url = '';

        isset($_SERVER['REQUEST_URI']) && $url .= $_SERVER['REQUEST_URI'];

        $url !== '/' && $url = rtrim($url, '/');

        $prefixController = 'App\\Controllers\\';

        $routeFound = false;
        $methodNotAllowed = false;
        $allowedMethods = [];
        
        foreach ($routes as $route) {
            $pattern = '#^'. preg_replace('/{id}/', '([\w-]+)', $route['path']) .'$#';

            if (preg_match($pattern, $url, $matches)) {
                array_shift($matches);
                $routeFound = true;
                $allowedMethods[] = $route['method'];

                if ($route['method'] === Request::method()) {
                    // Executa os middlewares se existirem
                    if (isset($route['middleware'])) {
                        foreach ($route['middleware'] as $middleware) {
                            $middleware = $middleware[0];
                            Middleware::$middleware();
                        }
                    }

                    [$controller, $action] = explode('@', $route['action']);
                    $controller = $prefixController . $controller;
                    $extendController = new $controller();
                    $extendController->$action(new Request, new Response, $matches);
                    return;
                }
            }
        }

        if ($routeFound) {
            Response::json([
                'error'   => true,
                'success' => false,
                'message' => 'Method not allowed. Allowed methods: ' . implode(', ', $allowedMethods)
            ], 405);
            return;
        }

        if (!$routeFound) {
            $controller = $prefixController . 'NotFoundController';
            $extendController = new $controller();
            $extendController->index(new Request, new Response);
        }
    }
}