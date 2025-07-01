<?php

namespace Koru;

use Koru\Exceptions\KoruException;

class ErrorHandler
{
    private Logger $logger;
    
    public function __construct()
    {
        $this->logger = new Logger();
    }
    
    public function register(): void
    {
        set_error_handler([$this, 'handleError']);
        set_exception_handler([$this, 'handleException']);
        register_shutdown_function([$this, 'handleShutdown']);
        
        // Production ortamında hata gösterimini kapat
        if (Environment::isProduction()) {
            ini_set('display_errors', '0');
            ini_set('log_errors', '1');
        } else {
            ini_set('display_errors', '1');
            error_reporting(E_ALL);
        }
    }
    
    public function handleError(int $severity, string $message, string $file, int $line): bool
    {
        if (!(error_reporting() & $severity)) {
            return false;
        }
        
        $exception = new \ErrorException($message, 0, $severity, $file, $line);
        $this->handleException($exception);
        
        return true;
    }
    
    public function handleException(\Throwable $e): void
    {
        try {
            $this->logger->logException($e);
            
            // Force development check
            $isDevelopment = Environment::isDevelopment() || 
                            Environment::isDebugging() || 
                            Config::get('APP_ENV') === 'development';
            
            if ($isDevelopment) {
                $this->renderDevelopmentError($e);
            } else {
                $this->renderProductionError($e);
            }
        } catch (\Throwable $logException) {
            $this->renderFallbackError($e);
        }
    }
    
    public function handleShutdown(): void
    {
        $error = error_get_last();
        
        if ($error && in_array($error['type'], [E_ERROR, E_CORE_ERROR, E_COMPILE_ERROR, E_PARSE])) {
            $exception = new \ErrorException($error['message'], 0, $error['type'], $error['file'], $error['line']);
            $this->handleException($exception);
        }
    }
    
    private function renderDevelopmentError(\Throwable $e): void
    {
        // Force development mode check
        $isDevelopment = Environment::isDevelopment() || Environment::isDebugging();
        
        if (!$isDevelopment) {
            // Eğer hala production modundaysa, force development gösterimi
            $isDevelopment = Config::get('APP_ENV') === 'development' || Config::get('APP_DEBUG') === 'true';
        }
        
        if ($isDevelopment) {
            http_response_code($this->getHttpStatusCode($e));
            echo $this->generateDevelopmentErrorHtml($e);
        } else {
            $this->renderProductionError($e);
        }
    }
    
    private function renderProductionError(\Throwable $e): void
    {
        http_response_code($this->getHttpStatusCode($e));
        
        if ($this->isAjaxRequest()) {
            header('Content-Type: application/json');
            echo json_encode([
                'error' => 'Bir hata oluştu. Lütfen daha sonra tekrar deneyin.',
                'code' => $e->getCode()
            ]);
        } else {
            echo $this->generateProductionErrorHtml($e);
        }
    }
    
    private function renderFallbackError(\Throwable $e): void
    {
        http_response_code(500);
        echo "<h1>Sistem Hatası</h1><p>Bir hata oluştu ve loglanamadı.</p>";
    }
    
    private function getHttpStatusCode(\Throwable $e): int
    {
        if ($e instanceof \Koru\Exceptions\RouteException) {
            return 404;
        }
        
        if ($e instanceof \Koru\Exceptions\ValidationException) {
            return 422;
        }
        
        return 500;
    }
    
    private function isAjaxRequest(): bool
    {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
               strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }
    
    private function generateDevelopmentErrorHtml(\Throwable $e): string
    {
        $trace = $this->formatStackTrace($e->getTrace());
        $context = '';
        
        if ($e instanceof KoruException) {
            $context = '<h3>Context:</h3><pre>' . htmlspecialchars(json_encode($e->getContext(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) . '</pre>';
        }
        
        if ($e instanceof \Koru\Exceptions\DatabaseException) {
            $context .= '<h3>SQL:</h3><pre>' . htmlspecialchars($e->getSql()) . '</pre>';
            $context .= '<h3>Bindings:</h3><pre>' . htmlspecialchars(json_encode($e->getBindings(), JSON_PRETTY_PRINT)) . '</pre>';
        }
        
        return "
        <!DOCTYPE html>
        <html>
        <head>
            <title>koruPHP Hata</title>
            <style>
                body { font-family: monospace; margin: 20px; background: #f5f5f5; }
                .container { background: white; padding: 20px; border-radius: 5px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
                .error-header { background: #e74c3c; color: white; padding: 15px; margin: -20px -20px 20px -20px; border-radius: 5px 5px 0 0; }
                .error-message { font-size: 18px; margin-bottom: 10px; }
                .error-location { font-size: 14px; opacity: 0.9; }
                .trace { background: #2c3e50; color: #ecf0f1; padding: 15px; border-radius: 5px; overflow-x: auto; }
                .trace-item { margin-bottom: 10px; padding: 5px; background: rgba(255,255,255,0.1); border-radius: 3px; }
                pre { margin: 0; white-space: pre-wrap; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='error-header'>
                    <div class='error-message'>" . htmlspecialchars($e->getMessage()) . "</div>
                    <div class='error-location'>" . htmlspecialchars($e->getFile()) . ":" . $e->getLine() . "</div>
                </div>
                {$context}
                <h3>Stack Trace:</h3>
                <div class='trace'>{$trace}</div>
            </div>
        </body>
        </html>";
    }
    
    private function generateProductionErrorHtml(\Throwable $e): string
    {
        return "
        <!DOCTYPE html>
        <html>
        <head>
            <title>Hata</title>
            <style>
                body { font-family: Arial, sans-serif; text-align: center; margin-top: 50px; background: #f5f5f5; }
                .container { max-width: 600px; margin: 0 auto; background: white; padding: 40px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
                h1 { color: #e74c3c; }
                p { color: #7f8c8d; }
            </style>
        </head>
        <body>
            <div class='container'>
                <h1>Bir Hata Oluştu</h1>
                <p>Üzgünüz, bir sistem hatası oluştu. Lütfen daha sonra tekrar deneyin.</p>
                <p>Sorun devam ederse sistem yöneticisiyle iletişime geçin.</p>
            </div>
        </body>
        </html>";
    }
    
    private function formatStackTrace(array $trace): string
    {
        $result = '';
        
        foreach ($trace as $index => $item) {
            $file = $item['file'] ?? 'unknown';
            $line = $item['line'] ?? 0;
            $class = $item['class'] ?? '';
            $function = $item['function'] ?? '';
            $type = $item['type'] ?? '';
            
            $result .= "<div class='trace-item'>";
            $result .= "<strong>#{$index}</strong> {$file}:{$line}<br>";
            $result .= "<span style='color: #3498db;'>{$class}{$type}{$function}()</span>";
            $result .= "</div>";
        }
        
        return $result;
    }
}