<?php

namespace Koru;

class Logger
{
    private string $logPath;
    private string $defaultLevel = 'info';
    
    const EMERGENCY = 'emergency';
    const ALERT = 'alert';
    const CRITICAL = 'critical';
    const ERROR = 'error';
    const WARNING = 'warning';
    const NOTICE = 'notice';
    const INFO = 'info';
    const DEBUG = 'debug';
    
    private array $levels = [
        self::EMERGENCY => 0,
        self::ALERT => 1,
        self::CRITICAL => 2,
        self::ERROR => 3,
        self::WARNING => 4,
        self::NOTICE => 5,
        self::INFO => 6,
        self::DEBUG => 7,
    ];
    
    public function __construct()
    {
        $this->logPath = Config::get('LOG_PATH', __DIR__ . '/../storage/logs');
        $this->ensureLogDirectoryExists();
    }
    
    private function ensureLogDirectoryExists(): void
    {
        if (!is_dir($this->logPath)) {
            mkdir($this->logPath, 0755, true);
        }
    }
    
    public function emergency(string $message, array $context = []): void
    {
        $this->log(self::EMERGENCY, $message, $context);
    }
    
    public function alert(string $message, array $context = []): void
    {
        $this->log(self::ALERT, $message, $context);
    }
    
    public function critical(string $message, array $context = []): void
    {
        $this->log(self::CRITICAL, $message, $context);
    }
    
    public function error(string $message, array $context = []): void
    {
        $this->log(self::ERROR, $message, $context);
    }
    
    public function warning(string $message, array $context = []): void
    {
        $this->log(self::WARNING, $message, $context);
    }
    
    public function notice(string $message, array $context = []): void
    {
        $this->log(self::NOTICE, $message, $context);
    }
    
    public function info(string $message, array $context = []): void
    {
        $this->log(self::INFO, $message, $context);
    }
    
    public function debug(string $message, array $context = []): void
    {
        $this->log(self::DEBUG, $message, $context);
    }
    
    public function log(string $level, string $message, array $context = []): void
    {
        $minLevel = Config::get('LOG_LEVEL', self::INFO);
        
        if ($this->levels[$level] > $this->levels[$minLevel]) {
            return;
        }
        
        $logEntry = $this->formatLogEntry($level, $message, $context);
        $filename = $this->getLogFilename($level);
        
        file_put_contents($filename, $logEntry, FILE_APPEND | LOCK_EX);
    }
    
    private function formatLogEntry(string $level, string $message, array $context): string
    {
        $timestamp = date('Y-m-d H:i:s');
        $contextString = empty($context) ? '' : ' ' . json_encode($context, JSON_UNESCAPED_UNICODE);
        
        return "[{$timestamp}] {$level}: {$message}{$contextString}" . PHP_EOL;
    }
    
    private function getLogFilename(string $level): string
    {
        $date = date('Y-m-d');
        
        // Kritik hatalar ayrı dosyaya
        if (in_array($level, [self::EMERGENCY, self::ALERT, self::CRITICAL, self::ERROR])) {
            return "{$this->logPath}/error-{$date}.log";
        }
        
        return "{$this->logPath}/app-{$date}.log";
    }
    
    public function logException(\Throwable $e, string $level = self::ERROR): void
    {
        $context = [
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString()
        ];
        
        if ($e instanceof \Koru\Exceptions\KoruException) {
            $context = array_merge($context, $e->getContext());
        }
        
        if ($e instanceof \Koru\Exceptions\DatabaseException) {
            $context['sql'] = $e->getSql();
            $context['bindings'] = $e->getBindings();
        }
        
        $this->log($level, $e->getMessage(), $context);
    }
}