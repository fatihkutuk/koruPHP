<?php

namespace Koru\Exceptions;

class KoruException extends \Exception
{
    protected array $context = [];
    
    public function __construct(string $message = "", int $code = 0, ?\Throwable $previous = null)
    {
        // Code parametresi string gelirse int'e çevir
        if (is_string($code)) {
            $code = 0; // Varsayılan değer
        }
        
        parent::__construct($message, $code, $previous);
    }
    
    public function getContext(): array
    {
        return $this->context;
    }
    
    public function setContext(array $context): void
    {
        $this->context = $context;
    }
}