<?php

namespace App\Middleware;

class AuthenticateMiddleware
{
    public function handle(): bool
    {
        try {
            // Session'ı başlat
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            
            // Debug log
            logger()->debug("AuthenticateMiddleware - Session check", [
                'session_status' => session_status(),
                'user_id_exists' => isset($_SESSION['user_id']),
                'user_id' => $_SESSION['user_id'] ?? null
            ]);
            
            // Kullanıcı giriş yapmış mı kontrol et
            if (!isset($_SESSION['user_id'])) {
                $this->handleUnauthenticated();
                return false;
            }
            
            // Kullanıcı hala aktif mi kontrol et
            $user = sql_one("SELECT * FROM users WHERE id = ? AND status = 'active'", [$_SESSION['user_id']]);
            
            if (!$user) {
                // Session'ı temizle
                unset($_SESSION['user_id']);
                $this->handleUnauthenticated();
                return false;
            }
            
            logger()->debug("AuthenticateMiddleware - Success", [
                'user_id' => $user['id'],
                'user_email' => $user['email']
            ]);
            
            return true;
            
        } catch (\Exception $e) {
            logger()->error("AuthenticateMiddleware error", [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            
            $this->handleUnauthenticated();
            return false;
        }
    }
    
    private function handleUnauthenticated(): void
    {
        // AJAX request kontrolü
        if ($this->isAjaxRequest()) {
            http_response_code(401);
            header('Content-Type: application/json');
            echo json_encode([
                'error' => 'Unauthorized',
                'message' => 'Authentication required',
                'redirect' => '/auth/login'
            ]);
        } else {
            // Normal request - login sayfasına yönlendir
            $currentUrl = $_SERVER['REQUEST_URI'] ?? '/';
            $redirectUrl = '/auth/login?redirect=' . urlencode($currentUrl);
            header("Location: {$redirectUrl}");
            exit;
        }
    }
    
    private function isAjaxRequest(): bool
    {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
               strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }
}