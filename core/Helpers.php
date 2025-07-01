<?php

if (!function_exists('app')) {
    function app(): \Koru\Application {
        global $app;
        return $app;
    }
}

if (!function_exists('db')) {
    function db(string $connection = null): \Koru\Database {
        return app()->getDatabase();
    }
}

if (!function_exists('logger')) {
    function logger(): \Koru\Logger {
        return app()->getLogger();
    }
}

if (!function_exists('log_info')) {
    function log_info(string $message, array $context = []): void {
        logger()->info($message, $context);
    }
}

if (!function_exists('log_error')) {
    function log_error(string $message, array $context = []): void {
        logger()->error($message, $context);
    }
}

if (!function_exists('log_debug')) {
    function log_debug(string $message, array $context = []): void {
        logger()->debug($message, $context);
    }
}

if (!function_exists('sql')) {
    function sql(string $query, array $bindings = [], string $connection = null): array {
        return db()->select($query, $bindings, $connection);
    }
}

if (!function_exists('sql_one')) {
    function sql_one(string $query, array $bindings = [], string $connection = null): ?array {
        return db()->selectOne($query, $bindings, $connection);
    }
}

if (!function_exists('sql_execute')) {
    function sql_execute(string $query, array $bindings = [], string $connection = null): bool {
        return db()->execute($query, $bindings, $connection);
    }
}

if (!function_exists('pdo')) {
    function pdo(string $connection = null): \PDO {
        return db()->pdo($connection);
    }
}

if (!function_exists('config')) {
    function config(string $key, $default = null) {
        return \Koru\Config::get($key, $default);
    }
}

if (!function_exists('url')) {
    function url(string $path = ''): string {
        $baseUrl = rtrim(config('APP_URL', 'http://localhost'), '/');
        return $baseUrl . '/' . ltrim($path, '/');
    }
}

if (!function_exists('redirect')) {
    function redirect(string $url): void {
        header("Location: {$url}");
        exit;
    }
}

if (!function_exists('view')) {
    function view(string $view, array $data = []): string {
        return app()->getView()->render($view, $data);
    }
}

if (!function_exists('json_response')) {
    function json_response(array $data, int $statusCode = 200): void {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
    }
}

if (!function_exists('env')) {
    function env(): string {
        return \Koru\Environment::get();
    }
}

if (!function_exists('is_dev')) {
    function is_dev(): bool {
        return \Koru\Environment::isDevelopment();
    }
}

if (!function_exists('is_debug')) {
    function is_debug(): bool {
        return \Koru\Environment::isDebugging();
    }
}
if (!function_exists('auth')) {
    function auth(): \Koru\Auth\AuthManager {
        static $authManager = null;
        if ($authManager === null) {
            $authManager = new \Koru\Auth\AuthManager();
        }
        return $authManager;
    }
}

if (!function_exists('user')) {
    function user(): ?\Koru\Auth\Interfaces\UserInterface {
        return auth()->user();
    }
}

if (!function_exists('can')) {
    function can(string $permission): bool {
        return auth()->can($permission);
    }
}

if (!function_exists('cannot')) {
    function cannot(string $permission): bool {
        return !can($permission);
    }
}

if (!function_exists('hasRole')) {
    function hasRole(string $role): bool {
        return auth()->hasRole($role);
    }
}

if (!function_exists('csrf_token')) {
    function csrf_token(): string {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        
        return $_SESSION['csrf_token'];
    }
}

if (!function_exists('auth_user_id')) {
    function auth_user_id(): string|int|null {
        return auth()->id();
    }
}