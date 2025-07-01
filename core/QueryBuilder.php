<?php

namespace Koru;

use PDO;
use Koru\Exceptions\DatabaseException;

class QueryBuilder
{
    private PDO $pdo;
    private Logger $logger;
    private bool $debug;
    private string $table = '';
    private array $wheres = [];
    private array $bindings = [];
    private string $selectClause = '*';
    private string $orderClause = '';
    private string $limitClause = '';
    
    public function __construct(PDO $pdo, Logger $logger = null, bool $debug = false)
    {
        $this->pdo = $pdo;
        $this->logger = $logger ?? new Logger();
        $this->debug = $debug;
    }
    
    // ... diğer metodlar aynı kalacak (önceki QueryBuilder kodunu kullanın)
    
    public function table(string $table): self
    {
        $this->table = $table;
        return $this;
    }
    
    public function select(string $columns = '*'): self
    {
        $this->selectClause = $columns;
        return $this;
    }
    
    public function where(string $column, string $operator, $value): self
    {
        $this->wheres[] = "{$column} {$operator} ?";
        $this->bindings[] = $value;
        return $this;
    }
    
    public function orderBy(string $column, string $direction = 'ASC'): self
    {
        $this->orderClause = "ORDER BY {$column} {$direction}";
        return $this;
    }
    
    public function limit(int $limit): self
    {
        $this->limitClause = "LIMIT {$limit}";
        return $this;
    }
    
    public function get(): array
    {
        $sql = "SELECT {$this->selectClause} FROM {$this->table}";
        
        if (!empty($this->wheres)) {
            $sql .= " WHERE " . implode(' AND ', $this->wheres);
        }
        
        if ($this->orderClause) {
            $sql .= " {$this->orderClause}";
        }
        
        if ($this->limitClause) {
            $sql .= " {$this->limitClause}";
        }
        
        if ($this->debug) {
            $this->logger->debug("QueryBuilder SQL", [
                'sql' => $sql,
                'bindings' => $this->bindings
            ]);
        }
        
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($this->bindings);
            return $stmt->fetchAll();
        } catch (\PDOException $e) {
            $exception = new DatabaseException($e->getMessage(), $e->getCode(), $e);
            $exception->setSqlInfo($sql, $this->bindings);
            throw $exception;
        }
    }
    
    public function first(): ?array
    {
        $results = $this->limit(1)->get();
        return $results[0] ?? null;
    }
    
    public function insert(array $data): bool
    {
        $columns = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));
        
        $sql = "INSERT INTO {$this->table} ({$columns}) VALUES ({$placeholders})";
        
        if ($this->debug) {
            $this->logger->debug("QueryBuilder INSERT", [
                'sql' => $sql,
                'data' => $data
            ]);
        }
        
        try {
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute(array_values($data));
        } catch (\PDOException $e) {
            $exception = new DatabaseException($e->getMessage(), $e->getCode(), $e);
            $exception->setSqlInfo($sql, array_values($data));
            throw $exception;
        }
    }
    
    public function update(array $data): bool
    {
        $setClause = implode(', ', array_map(fn($key) => "{$key} = ?", array_keys($data)));
        
        $sql = "UPDATE {$this->table} SET {$setClause}";
        
        $bindings = array_values($data);
        
        if (!empty($this->wheres)) {
            $sql .= " WHERE " . implode(' AND ', $this->wheres);
            $bindings = array_merge($bindings, $this->bindings);
        }
        
        if ($this->debug) {
            $this->logger->debug("QueryBuilder UPDATE", [
                'sql' => $sql,
                'bindings' => $bindings
            ]);
        }
        
        try {
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute($bindings);
        } catch (\PDOException $e) {
            $exception = new DatabaseException($e->getMessage(), $e->getCode(), $e);
            $exception->setSqlInfo($sql, $bindings);
            throw $exception;
        }
    }
    
    public function delete(): bool
    {
        $sql = "DELETE FROM {$this->table}";
        
        if (!empty($this->wheres)) {
            $sql .= " WHERE " . implode(' AND ', $this->wheres);
        }
        
        if ($this->debug) {
            $this->logger->debug("QueryBuilder DELETE", [
                'sql' => $sql,
                'bindings' => $this->bindings
            ]);
        }
        
        try {
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute($this->bindings);
        } catch (\PDOException $e) {
            $exception = new DatabaseException($e->getMessage(), $e->getCode(), $e);
            $exception->setSqlInfo($sql, $this->bindings);
            throw $exception;
        }
    }
}