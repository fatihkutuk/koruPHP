<?php

namespace App\Middleware;

class AdminMiddleware
{
    public function handle(): bool
    {
        // Session kontrolü
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!isset($_SESSION['user_id'])) {
            $this->handleUnauthorized('Authentication required');
            return false;
        }
        
        // Kullanıcının admin rolü var mı kontrol et
        $user = sql_one("SELECT role FROM users WHERE id = ? AND status = 'active'", [$_SESSION['user_id']]);
        
        if (!$user || $user['role'] !== 'admin') {
            $this->handleUnauthorized('Admin access required');
            return false;
        }
        
        return true;
    }
    
    private function handleUnauthorized(string $message): void
    {
        if ($this->isAjaxRequest()) {
            http_response_code(403);
            header('Content-Type: application/json');
            echo json_encode(['error' => $message]);
        } else {
            http_response_code(403);
            echo "<h1>Erişim Reddedildi</h1><p>{$message}</p><p><a href='/auth/login'>Giriş Yap</a></p>";
        }
    }
    
    private function isAjaxRequest(): bool
    {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
               strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }
}