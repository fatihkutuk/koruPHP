<?php

namespace App\Middleware;

class GuestMiddleware
{
    public function handle(): bool
    {
        // Session kontrolü
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Kullanıcı giriş yapmışsa dashboard'a yönlendir
        if (isset($_SESSION['user_id'])) {
            if ($this->isAjaxRequest()) {
                http_response_code(302);
                header('Content-Type: application/json');
                echo json_encode(['redirect' => '/dashboard']);
            } else {
                header('Location: /dashboard');
                exit;
            }
            return false;
        }
        
        return true;
    }
    
    private function isAjaxRequest(): bool
    {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
               strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }
}