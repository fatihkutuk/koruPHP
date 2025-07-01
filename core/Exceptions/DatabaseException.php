<?php

namespace Koru\Exceptions;

class DatabaseException extends KoruException
{
    private string $sql = '';
    private array $bindings = [];
    
    public function setSqlInfo(string $sql, array $bindings = []): void
    {
        $this->sql = $sql;
        $this->bindings = $bindings;
    }
    
    public function getSql(): string
    {
        return $this->sql;
    }
    
    public function getBindings(): array
    {
        return $this->bindings;
    }
}