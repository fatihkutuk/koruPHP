<?php

namespace Koru\Auth\Providers;

use Koru\Auth\Interfaces\AuthProviderInterface;
use Koru\Auth\Interfaces\UserInterface;
use Koru\Auth\User;

class SessionAuthProvider implements AuthProviderInterface
{
    public function authenticate(array $credentials): ?UserInterface
    {
        $user = sql_one("
            SELECT * FROM users 
            WHERE email = ? AND status = 'active'
        ", [$credentials['email'] ?? '']);
        
        if ($user && password_verify($credentials['password'] ?? '', $user['password'])) {
            // Session başlat
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['login_time'] = time();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            
            // Son giriş bilgilerini güncelle
            sql_execute("
                UPDATE users 
                SET last_login = NOW(), login_count = login_count + 1 
                WHERE id = ?
            ", [$user['id']]);
            
            logger()->info("Session authentication successful", [
                'user_id' => $user['id'],
                'email' => $user['email']
            ]);
            
            return new User($user);
        }
        
        return null;
    }
    
    public function validateToken(string $token): ?UserInterface
    {
        // Session provider için token validation
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (isset($_SESSION['user_id'])) {
            $user = sql_one("
                SELECT * FROM users 
                WHERE id = ? AND status = 'active'
            ", [$_SESSION['user_id']]);
            
            if ($user) {
                return new User($user);
            }
        }
        
        return null;
    }
    
    public function logout(string $token = null): bool
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Session'ı temizle
        $_SESSION = [];
        
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        
        session_destroy();
        
        logger()->info("Session logout completed");
        
        return true;
    }
    
    public function refreshToken(string $refreshToken): ?array
    {
        // Session'da refresh token yok
        return null;
    }
    
    public function getName(): string
    {
        return 'session';
    }
    
    public function getCapabilities(): array
    {
        return [
            'stateful' => true,
            'csrf_protection' => true,
            'remember_me' => true,
            'browser_based' => true
        ];
    }
}