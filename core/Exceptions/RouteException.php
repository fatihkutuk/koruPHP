<?php

namespace Koru\Exceptions;

class RouteException extends KoruException
{
    private string $method = '';
    private string $uri = '';
    
    public function __construct(string $message, string $method = '', string $uri = '')
    {
        parent::__construct($message);
        $this->method = $method;
        $this->uri = $uri;
    }
    
    public function getMethod(): string
    {
        return $this->method;
    }
    
    public function getUri(): string
    {
        return $this->uri;
    }
}