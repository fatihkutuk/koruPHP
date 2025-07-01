<?php

namespace Koru;

use Dotenv\Dotenv;

class Application
{
    private Router $router;
    private View $view;
    private Database $database;
    private Logger $logger;
    private ErrorHandler $errorHandler;
    private array $middleware = [];
    private string $currentModule = 'web';
    
    public function __construct()
    {
        $this->loadEnvironment();
        Environment::detect();
        
        $this->logger = new Logger();
        $this->errorHandler = new ErrorHandler();
        $this->errorHandler->register();
        
        $this->router = new Router($this->logger);
        $this->view = new View();
        $this->database = new Database();
        
        $this->logger->info("Application initialized", [
            'environment' => Environment::get(),
            'debug' => Environment::isDebugging()
        ]);
    }
    
    private function loadEnvironment(): void
    {
        if (file_exists(__DIR__ . '/../.env')) {
            $dotenv = Dotenv::createImmutable(__DIR__ . '/..');
            $dotenv->load();
        }
    }
    
    public function setModule(string $module): void
    {
        $this->currentModule = $module;
        
        // Modül özel env dosyasını yükle
        $moduleEnvPath = __DIR__ . "/../modules/{$module}/.env";
        if (file_exists($moduleEnvPath)) {
            $dotenv = Dotenv::createImmutable(dirname($moduleEnvPath));
            $dotenv->load();
        }
        
        // Modül rotalarını yükle
        $routesPath = __DIR__ . "/../modules/{$module}/routes.php";
        if (file_exists($routesPath)) {
            $this->logger->debug("Loading routes from: {$routesPath}");
            require $routesPath;
        } else {
            $this->logger->error("Routes file not found: {$routesPath}");
            throw new \Exception("Routes file not found for module: {$module}");
        }
    }
    
    public function addMiddleware(string $middleware): void
    {
        $this->middleware[] = $middleware;
    }
    
    public function run(): void
    {
        try {
            $uri = $_SERVER['REQUEST_URI'] ?? '/';
            $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
            
            // Query string'i kaldır
            if (($pos = strpos($uri, '?')) !== false) {
                $uri = substr($uri, 0, $pos);
            }
            
            $this->logger->info("Request started", [
                'method' => $method,
                'uri' => $uri,
                'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? '',
                'ip' => $_SERVER['REMOTE_ADDR'] ?? ''
            ]);
            
            // API ve Web route dosyalarını yükle
            $this->loadRoutes();
            
            // Router'ı çalıştır
            $this->router->dispatch($method, $uri);
            
        } catch (\Throwable $e) {
            $this->handleError($e);
        }
    }
    private function loadRoutes(): void
    {
        // Web routes
        $webRoutesPath = __DIR__ . "/../modules/web/routes.php";
        if (file_exists($webRoutesPath)) {
            $this->logger->info("Loading routes from: " . $webRoutesPath);
            require $webRoutesPath;
        }
        
        // API routes
        $apiRoutesPath = __DIR__ . "/../modules/api/routes.php";
        if (file_exists($apiRoutesPath)) {
            $this->logger->info("Loading API routes from: " . $apiRoutesPath);
            require $apiRoutesPath;
        }
    }
    public function getRouter(): Router
    {
        return $this->router;
    }
    
    public function getView(): View
    {
        return $this->view;
    }
    
    public function getDatabase(): Database
    {
        return $this->database;
    }
    
    public function getLogger(): Logger
    {
        return $this->logger;
    }
}