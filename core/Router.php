<?php

namespace Koru;

use Koru\Exceptions\RouteException;

class Router
{
    private array $routes = [];
    private array $groups = [];
    private string $currentGroup = '';
    private Logger $logger;
    
    public function __construct(Logger $logger = null)
    {
        $this->logger = $logger ?? new Logger();
    }
    
    public function get(string $path, $handler): void
    {
        $this->addRoute('GET', $path, $handler);
    }
    
    public function post(string $path, $handler): void
    {
        $this->addRoute('POST', $path, $handler);
    }
    
    public function put(string $path, $handler): void
    {
        $this->addRoute('PUT', $path, $handler);
    }
    
    public function delete(string $path, $handler): void
    {
        $this->addRoute('DELETE', $path, $handler);
    }
    
    public function group(array $attributes, callable $callback): void
    {
        $previousGroup = $this->currentGroup;
        
        $prefix = $attributes['prefix'] ?? '';
        $this->currentGroup = $previousGroup . $prefix;
        
        if (isset($attributes['middleware'])) {
            $this->groups[$this->currentGroup]['middleware'] = $attributes['middleware'];
        }
        
        if (isset($attributes['namespace'])) {
            $this->groups[$this->currentGroup]['namespace'] = $attributes['namespace'];
        }
        
        $callback($this);
        
        $this->currentGroup = $previousGroup;
    }
    
    private function addRoute(string $method, string $path, $handler): void
    {
        $fullPath = $this->currentGroup . $path;
        $this->routes[$method][$fullPath] = [
            'handler' => $handler,
            'middleware' => $this->groups[$this->currentGroup]['middleware'] ?? [],
            'namespace' => $this->groups[$this->currentGroup]['namespace'] ?? ''
        ];
        
        $this->logger->debug("Route added", [
            'method' => $method,
            'path' => $fullPath,
            'handler' => is_string($handler) ? $handler : 'Closure'
        ]);
    }
    
    public function dispatch(string $method, string $uri): void
    {
        if (!isset($this->routes[$method])) {
            $this->notFound($method, $uri);
            return;
        }
        
        foreach ($this->routes[$method] as $pattern => $route) {
            $params = $this->matchRoute($pattern, $uri);
            if ($params !== false) {
                $this->logger->info("Route matched", [
                    'method' => $method,
                    'uri' => $uri,
                    'pattern' => $pattern,
                    'params' => $params
                ]);
                
                $this->callHandler($route, $params);
                return;
            }
        }
        
        $this->notFound($method, $uri);
    }
    
    private function matchRoute(string $pattern, string $uri): array|false
    {
        // Dinamik parametreleri işle {id}, {slug} vs.
        $pattern = preg_replace('/\{([^}]+)\}/', '([^/]+)', $pattern);
        $pattern = str_replace('/', '\/', $pattern);
        
        if (preg_match('/^' . $pattern . '$/', $uri, $matches)) {
            array_shift($matches); // İlk elemanı kaldır (tam eşleşme)
            return $matches;
        }
        
        return false;
    }
    
    private function callHandler(array $route, array $params): void
    {
        try {
            // Middleware'leri çalıştır - DÜZELTME BURADA
            if (!empty($route['middleware'])) {
                $this->runMiddlewares($route['middleware']);
            }
            
            $handler = $route['handler'];
            
            if (is_string($handler)) {
                // Controller@method formatı
                [$controllerName, $method] = explode('@', $handler);
                
                // Namespace ekle
                if (!empty($route['namespace'])) {
                    $controllerName = $route['namespace'] . '\\' . $controllerName;
                }
                
                if (!class_exists($controllerName)) {
                    throw new RouteException("Controller not found: {$controllerName}");
                }
                
                $controller = new $controllerName();
                
                if (!method_exists($controller, $method)) {
                    throw new RouteException("Method {$method} not found in {$controllerName}");
                }
                
                call_user_func_array([$controller, $method], $params);
            } elseif (is_callable($handler)) {
                call_user_func_array($handler, $params);
            } else {
                throw new RouteException("Invalid route handler");
            }
        } catch (\Throwable $e) {
            $this->logger->error("Route handler error", [
                'handler' => is_string($route['handler']) ? $route['handler'] : 'Closure',
                'params' => $params,
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            
            throw $e;
        }
    }
    
    private function runMiddlewares(array $middlewares): void
    {
        foreach ($middlewares as $middleware) {
            try {
                $this->logger->debug("Running middleware: {$middleware}");
                
                // Permission middleware özel durumu
                if (strpos($middleware, 'permission:') === 0) {
                    $permission = substr($middleware, 11);
                    $this->checkPermission($permission);
                    continue;
                }
                
                // Middleware class'ını resolve et
                $middlewareClass = $this->resolveMiddlewareClass($middleware);
                
                $this->logger->debug("Resolved middleware class: {$middlewareClass}");
                
                // Class var mı kontrol et
                if (!class_exists($middlewareClass)) {
                    $this->logger->error("Middleware class not found: {$middlewareClass}");
                    http_response_code(500);
                    echo json_encode(['error' => "Middleware class not found: {$middlewareClass}"]);
                    exit;
                }
                
                // Middleware instance oluştur
                $middlewareInstance = new $middlewareClass();
                
                // Handle metodu var mı kontrol et
                if (!method_exists($middlewareInstance, 'handle')) {
                    $this->logger->error("Middleware handle method not found: {$middlewareClass}");
                    http_response_code(500);
                    echo json_encode(['error' => "Middleware handle method not found"]);
                    exit;
                }
                
                // Middleware'i çalıştır
                $result = $middlewareInstance->handle();
                
                $this->logger->debug("Middleware result", [
                    'middleware' => $middlewareClass,
                    'result' => $result
                ]);
                
                // Eğer false döndürürse isteği durdur
                if ($result === false) {
                    $this->logger->info("Request blocked by middleware: {$middlewareClass}");
                    exit;
                }
                
            } catch (\Throwable $e) {
                $this->logger->error("Middleware execution error", [
                    'middleware' => $middleware,
                    'error' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'trace' => $e->getTraceAsString()
                ]);
                
                // Development'ta detaylı hata göster
                if (\Koru\Environment::isDebugging()) {
                    http_response_code(500);
                    echo "<h1>Middleware Error</h1>";
                    echo "<p><strong>Middleware:</strong> {$middleware}</p>";
                    echo "<p><strong>Error:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
                    echo "<p><strong>File:</strong> " . htmlspecialchars($e->getFile()) . ":" . $e->getLine() . "</p>";
                    echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
                } else {
                    http_response_code(500);
                    echo json_encode(['error' => 'Middleware execution failed']);
                }
                exit;
            }
        }
    }
    
    private function resolveMiddlewareClass(string $middleware): string
    {
        // Middleware alias'ları
        $aliases = [
            'auth' => 'App\\Middleware\\AuthenticateMiddleware',
            'admin' => 'App\\Middleware\\AdminMiddleware',
            'guest' => 'App\\Middleware\\GuestMiddleware'
        ];
        
        // Alias varsa döndür, yoksa olduğu gibi döndür
        return $aliases[$middleware] ?? $middleware;
    }
    
    private function checkPermission(string $permission): void
    {
        // Session kontrolü
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            if ($this->isAjaxRequest()) {
                echo json_encode(['error' => 'Authentication required']);
            } else {
                echo "<h1>Authentication Required</h1>";
            }
            exit;
        }
        
        // Admin kontrolü (admin her şeyi yapabilir)
        $user = sql_one("SELECT role FROM users WHERE id = ?", [$_SESSION['user_id']]);
        
        if ($user && $user['role'] === 'admin') {
            return; // Admin geçebilir
        }
        
        // Specific permission kontrolü
        $hasPermission = sql_one("
            SELECT 1 FROM user_permissions up
            JOIN permissions p ON up.permission_id = p.id
            WHERE up.user_id = ? AND p.name = ?
        ", [$_SESSION['user_id'], $permission]);
        
        if (!$hasPermission) {
            http_response_code(403);
            if ($this->isAjaxRequest()) {
                echo json_encode(['error' => "Permission '{$permission}' required"]);
            } else {
                echo "<h1>Permission Required</h1><p>'{$permission}' permission required</p>";
            }
            exit;
        }
    }
    
    private function notFound(string $method, string $uri): void
    {
        $this->logger->warning("Route not found", [
            'method' => $method,
            'uri' => $uri
        ]);
        
        http_response_code(404);
        
        if ($this->isAjaxRequest()) {
            header('Content-Type: application/json');
            echo json_encode(['error' => '404 - Route not found']);
        } else {
            echo "
            <!DOCTYPE html>
            <html>
            <head>
                <title>404 - Sayfa Bulunamadı</title>
                <style>
                    body { font-family: Arial, sans-serif; text-align: center; margin-top: 50px; }
                    h1 { color: #e74c3c; }
                </style>
            </head>
            <body>
                <h1>404 - Sayfa Bulunamadı</h1>
                <p>Aradığınız sayfa bulunamadı: {$method} {$uri}</p>
                <p><a href='/'>Ana Sayfaya Dön</a> | <a href='/test'>Test Sayfası</a></p>
            </body>
            </html>";
        }
    }
    
    private function isAjaxRequest(): bool
    {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
               strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }
}