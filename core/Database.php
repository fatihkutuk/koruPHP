<?php

namespace Koru;

use PDO;
use PDOException;
use Koru\Exceptions\DatabaseException;

class Database
{
    private array $connections = [];
    private string $defaultConnection = 'default';
    private Logger $logger;
    private bool $debug = false;
    
    public function __construct()
    {
        $this->logger = new Logger();
        $this->debug = Environment::isDebugging();
    }
    
    /**
     * Bağlantı al (connection pooling)
     */
    public function connection(string $name = null): PDO
    {
        $connectionName = $name ?? $this->defaultConnection;
        
        if (!isset($this->connections[$connectionName])) {
            $this->connections[$connectionName] = $this->createConnection($connectionName);
        }
        
        return $this->connections[$connectionName];
    }
    
    /**
     * Doğrudan PDO instance'ına erişim
     */
    public function pdo(string $connection = null): PDO
    {
        return $this->connection($connection);
    }
    
    /**
     * Query builder ile sorgu
     */
    public function query(string $connection = null): QueryBuilder
    {
        return new QueryBuilder($this->connection($connection), $this->logger, $this->debug);
    }
    
    /**
     * Doğrudan SQL SELECT sorgusu
     */
    public function select(string $sql, array $bindings = [], string $connection = null): array
    {
        $startTime = microtime(true);
        
        try {
            if ($this->debug) {
                $this->logger->debug("SQL Query", [
                    'sql' => $sql,
                    'bindings' => $bindings,
                    'connection' => $connection ?? $this->defaultConnection
                ]);
            }
            
            $stmt = $this->connection($connection)->prepare($sql);
            $stmt->execute($bindings);
            $result = $stmt->fetchAll();
            
            if ($this->debug) {
                $executionTime = (microtime(true) - $startTime) * 1000;
                $this->logger->debug("Query executed", [
                    'execution_time_ms' => round($executionTime, 2),
                    'rows_returned' => count($result)
                ]);
            }
            
            return $result;
            
        } catch (PDOException $e) {
            $this->handleDatabaseException($e, $sql, $bindings);
        }
    }
    
    /**
     * Tek kayıt getir
     */
    public function selectOne(string $sql, array $bindings = [], string $connection = null): ?array
    {
        $results = $this->select($sql, $bindings, $connection);
        return $results[0] ?? null;
    }
    
    /**
     * INSERT, UPDATE, DELETE sorguları
     */
    public function execute(string $sql, array $bindings = [], string $connection = null): bool
    {
        $startTime = microtime(true);
        
        try {
            if ($this->debug) {
                $this->logger->debug("SQL Execute", [
                    'sql' => $sql,
                    'bindings' => $bindings,
                    'connection' => $connection ?? $this->defaultConnection
                ]);
            }
            
            $stmt = $this->connection($connection)->prepare($sql);
            $result = $stmt->execute($bindings);
            
            if ($this->debug) {
                $executionTime = (microtime(true) - $startTime) * 1000;
                $this->logger->debug("Query executed", [
                    'execution_time_ms' => round($executionTime, 2),
                    'rows_affected' => $stmt->rowCount()
                ]);
            }
            
            return $result;
            
        } catch (PDOException $e) {
            $this->handleDatabaseException($e, $sql, $bindings);
        }
    }
    
    /**
     * INSERT ile ID getir
     */
    public function insertGetId(string $sql, array $bindings = [], string $connection = null): int
    {
        $this->execute($sql, $bindings, $connection);
        return (int) $this->connection($connection)->lastInsertId();
    }
    
    /**
     * Etkilenen kayıt sayısı
     */
    public function executeWithCount(string $sql, array $bindings = [], string $connection = null): int
    {
        try {
            $stmt = $this->connection($connection)->prepare($sql);
            $stmt->execute($bindings);
            return $stmt->rowCount();
        } catch (PDOException $e) {
            $this->handleDatabaseException($e, $sql, $bindings);
        }
    }
    
    /**
     * Transaction yönetimi
     */
    public function transaction(callable $callback, string $connection = null): mixed
    {
        $pdo = $this->connection($connection);
        
        try {
            $pdo->beginTransaction();
            
            $result = $callback($this);
            
            $pdo->commit();
            
            if ($this->debug) {
                $this->logger->debug("Transaction committed successfully");
            }
            
            return $result;
            
        } catch (\Throwable $e) {
            $pdo->rollBack();
            
            $this->logger->error("Transaction rolled back", [
                'error' => $e->getMessage(),
                'connection' => $connection ?? $this->defaultConnection
            ]);
            
            throw $e;
        }
    }
    
    /**
     * Toplu işlemler
     */
    public function batch(array $queries, string $connection = null): bool
    {
        return $this->transaction(function($db) use ($queries, $connection) {
            foreach ($queries as $query) {
                if (is_array($query)) {
                    $db->execute($query['sql'], $query['bindings'] ?? [], $connection);
                } else {
                    $db->execute($query, [], $connection);
                }
            }
            return true;
        }, $connection);
    }
    
    /**
     * Prepared statement oluştur
     */
    public function prepare(string $sql, string $connection = null): \PDOStatement
    {
        try {
            return $this->connection($connection)->prepare($sql);
        } catch (PDOException $e) {
            $this->handleDatabaseException($e, $sql, []);
        }
    }
    
    /**
     * Bağlantı oluştur
     */
    private function createConnection(string $name): PDO
    {
        $prefix = strtoupper($name) . '_DB_';
        
        $config = [
            'driver' => Config::get($prefix . 'DRIVER', 'mysql'),
            'host' => Config::get($prefix . 'HOST', 'localhost'),
            'port' => Config::get($prefix . 'PORT', '3306'),
            'database' => Config::get($prefix . 'DATABASE', 'koruphp'),
            'username' => Config::get($prefix . 'USERNAME', 'root'),
            'password' => Config::get($prefix . 'PASSWORD', ''),
            'charset' => Config::get($prefix . 'CHARSET', 'utf8mb4'),
            'collation' => Config::get($prefix . 'COLLATION', 'utf8mb4_unicode_ci'),
        ];
        
        $dsn = $this->buildDsn($config);
        
        try {
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::ATTR_PERSISTENT => Config::get($prefix . 'PERSISTENT', false),
            ];
            
            // MySQL için ek ayarlar
            if ($config['driver'] === 'mysql') {
                $options[PDO::MYSQL_ATTR_INIT_COMMAND] = "SET NAMES {$config['charset']} COLLATE {$config['collation']}";
                $options[PDO::MYSQL_ATTR_USE_BUFFERED_QUERY] = true;
            }
            
            $pdo = new PDO($dsn, $config['username'], $config['password'], $options);
            
            $this->logger->info("Database connection established", [
                'connection' => $name,
                'driver' => $config['driver'],
                'host' => $config['host'],
                'database' => $config['database']
            ]);
            
            return $pdo;
            
        } catch (PDOException $e) {
            $this->logger->critical("Database connection failed", [
                'connection' => $name,
                'error' => $e->getMessage(),
                'config' => array_diff_key($config, ['password' => ''])
            ]);
            
            throw new DatabaseException("Database connection failed: " . $e->getMessage(), $e->getCode(), $e);
        }
    }
    
    private function buildDsn(array $config): string
    {
        switch ($config['driver']) {
            case 'mysql':
                return "mysql:host={$config['host']};port={$config['port']};dbname={$config['database']};charset={$config['charset']}";
            
            case 'pgsql':
                return "pgsql:host={$config['host']};port={$config['port']};dbname={$config['database']}";
            
            case 'sqlite':
                return "sqlite:{$config['database']}";
            
            case 'sqlsrv':
                return "sqlsrv:Server={$config['host']},{$config['port']};Database={$config['database']}";
            
            default:
                throw new DatabaseException("Unsupported database driver: {$config['driver']}");
        }
    }
    
    private function handleDatabaseException(PDOException $e, string $sql, array $bindings): void
    {
        // PDO error code'u int'e çevir
        $errorCode = is_numeric($e->getCode()) ? (int)$e->getCode() : 0;
        
        $exception = new DatabaseException($e->getMessage(), $errorCode, $e);
        $exception->setSqlInfo($sql, $bindings);
        
        $this->logger->error("Database error", [
            'sql' => $sql,
            'bindings' => $bindings,
            'error' => $e->getMessage(),
            'code' => $e->getCode()
        ]);
        
        throw $exception;
    }
    /**
     * Bağlantı durumunu kontrol et
     */
    public function isConnected(string $connection = null): bool
    {
        try {
            $pdo = $this->connection($connection);
            $pdo->query('SELECT 1');
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
    
    /**
     * Tüm bağlantıları kapat
     */
    public function disconnect(): void
    {
        $this->connections = [];
        $this->logger->info("All database connections closed");
    }
}