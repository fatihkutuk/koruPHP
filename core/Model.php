<?php

namespace Koru;

class Model
{
    protected Database $database;
    protected string $table = '';
    protected string $primaryKey = 'id';
    
    public function __construct()
    {
        $this->database = app()->getDatabase();
        
        if (empty($this->table)) {
            // Sınıf adından tablo adını otomatik çıkar
            $className = (new \ReflectionClass($this))->getShortName();
            $this->table = strtolower($className) . 's';
        }
    }
    
    public function all(): array
    {
        return $this->database->query()->table($this->table)->get();
    }
    
    public function find(int $id): ?array
    {
        return $this->database->query()
            ->table($this->table)
            ->where($this->primaryKey, '=', $id)
            ->first();
    }
    
    public function create(array $data): bool
    {
        return $this->database->query()->table($this->table)->insert($data);
    }
    
    public function update(int $id, array $data): bool
    {
        return $this->database->query()
            ->table($this->table)
            ->where($this->primaryKey, '=', $id)
            ->update($data);
    }
    
    public function delete(int $id): bool
    {
        return $this->database->query()
            ->table($this->table)
            ->where($this->primaryKey, '=', $id)
            ->delete();
    }
    
    protected function query(): QueryBuilder
    {
        return $this->database->query()->table($this->table);
    }
    
    // ====== RAW SQL METODLARI ======
    
    /**
     * Doğrudan SQL SELECT sorgusu
     */
    protected function select(string $sql, array $bindings = []): array
    {
        return $this->database->select($sql, $bindings);
    }
    
    /**
     * Tek kayıt getir
     */
    protected function selectOne(string $sql, array $bindings = []): ?array
    {
        return $this->database->selectOne($sql, $bindings);
    }
    
    /**
     * SQL sorgusu çalıştır
     */
    protected function execute(string $sql, array $bindings = []): bool
    {
        return $this->database->execute($sql, $bindings);
    }
    
    /**
     * INSERT ile ID getir
     */
    protected function insertGetId(string $sql, array $bindings = []): int
    {
        return $this->database->insertGetId($sql, $bindings);
    }
    
    /**
     * Etkilenen kayıt sayısı ile çalıştır
     */
    protected function executeWithCount(string $sql, array $bindings = []): int
    {
        return $this->database->executeWithCount($sql, $bindings);
    }
}