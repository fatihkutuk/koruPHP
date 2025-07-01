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
            
            // Mevcut URL'i al
            $currentUrl = $_SERVER['REQUEST_URI'] ?? '/';
            
            // Login sayfalarını kontrol et - bunları redirect etme
            if ($this->isAuthPage($currentUrl)) {
                return true; // Middleware'i geç
            }
            
            // Debug log
            logger()->debug("AuthenticateMiddleware - Session check", [
                'session_status' => session_status(),
                'user_id_exists' => isset($_SESSION['user_id']),
                'user_id' => $_SESSION['user_id'] ?? null,
                'current_url' => $currentUrl
            ]);
            
            // Kullanıcı giriş yapmış mı kontrol et
            if (!isset($_SESSION['user_id'])) {
                $this->handleUnauthenticated($currentUrl);
                return false;
            }
            
            // Kullanıcı hala mevcut mu kontrol et
            $user = sql_one("SELECT * FROM users WHERE id = ?", [$_SESSION['user_id']]);
            
            if (!$user) {
                // Session'ı temizle
                unset($_SESSION['user_id']);
                $this->handleUnauthenticated($currentUrl);
                return false;
            }
            
            logger()->debug("AuthenticateMiddleware - Success", [
                'user_id' => $user['id'],
                'user_email' => $user['email'] ?? 'unknown'
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
    
    /**
     * Auth sayfalarını kontrol et
     */
    private function isAuthPage(string $url): bool
    {
        $authPaths = [
            '/auth/login',
            '/auth/register',
            '/auth/forgot-password',
            '/test/create-test-users'
        ];
        
        foreach ($authPaths as $path) {
            if (str_starts_with($url, $path)) {
                return true;
            }
        }
        
        return false;
    }
    
    private function handleUnauthenticated(string $currentUrl = '/'): void
    {
        // Redirect döngüsünü önle
        if (str_starts_with($currentUrl, '/auth/login')) {
            $currentUrl = '/dashboard'; // Varsayılan hedef
        }
        
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
            $redirectUrl = '/auth/login';
            
            // Sadece dashboard dışındaki sayfalar için redirect parametresi ekle
            if ($currentUrl !== '/' && $currentUrl !== '/dashboard' && !str_starts_with($currentUrl, '/auth/')) {
                $redirectUrl .= '?redirect=' . urlencode($currentUrl);
            }
            
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