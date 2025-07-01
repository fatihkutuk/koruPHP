<?php

namespace App\Web\Controllers;

use Koru\Controller;

class AuthController extends Controller
{
    public function showLogin(): void
    {
        // Session'ı başlat
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Redirect parametresi kontrol et
        $redirect = $_GET['redirect'] ?? '/dashboard';
        
        // Zaten giriş yapmışsa yönlendir
        if (isset($_SESSION['user_id'])) {
            $user = sql_one("SELECT * FROM users WHERE id = ?", [$_SESSION['user_id']]);
            if ($user) {
                redirect($redirect);
                return;
            } else {
                // Geçersiz session, temizle
                unset($_SESSION['user_id']);
            }
        }
        
        echo '<!DOCTYPE html>
<html>
<head>
    <title>Giriş Yap - koruPHP</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 400px; margin: 50px auto; padding: 20px; background: #f5f5f5; }
        .login-container { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input[type="email"], input[type="password"] { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box; }
        .remember-me { margin: 15px 0; }
        button { width: 100%; padding: 12px; background: #007cba; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 16px; }
        button:hover { background: #005a87; }
        .error { color: #dc3545; margin-bottom: 15px; padding: 10px; background: #f8d7da; border-radius: 4px; }
        .info { background: #e7f3ff; padding: 15px; border-left: 4px solid #007cba; margin-bottom: 20px; border-radius: 4px; }
        .links { text-align: center; margin-top: 20px; }
        .links a { color: #007cba; text-decoration: none; }
        .debug { background: #f8f9fa; padding: 10px; margin: 10px 0; border-radius: 4px; font-size: 12px; }
    </style>
</head>
<body>
    <div class="login-container">
        <h1>koruPHP - Giriş Yap</h1>
        
        <div class="debug">
            <strong>Debug:</strong> Session ID: ' . session_id() . '<br>
            <strong>Redirect to:</strong> ' . htmlspecialchars($redirect) . '
        </div>
        
        <div class="info">
            <strong>Test Kullanıcıları:</strong><br>
            Admin: test.admin@koruphp.com / password123<br>
            User: test.user@koruphp.com / password123<br>
            Operator: test.operator@koruphp.com / password123
        </div>
        
        <form method="POST" action="/auth/login">
            <input type="hidden" name="redirect" value="' . htmlspecialchars($redirect) . '">
            
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required autofocus>
            </div>
            
            <div class="form-group">
                <label for="password">Şifre:</label>
                <input type="password" id="password" name="password" required>
            </div>
            
            <div class="remember-me">
                <label>
                    <input type="checkbox" name="remember" value="1"> Beni Hatırla (30 gün)
                </label>
            </div>
            
            <button type="submit">Giriş Yap</button>
        </form>
        
        <div class="links">
            <a href="/test">Test Sayfasına Dön</a> |
            <a href="/test/create-test-users">Test Kullanıcıları Oluştur</a>
        </div>
    </div>
</body>
</html>';
    }
    
    public function login(): void
    {
        // Session'ı başlat
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        $remember = isset($_POST['remember']);
        $redirect = $_POST['redirect'] ?? '/dashboard';
        
        // Validation
        if (empty($email) || empty($password)) {
            $this->showLoginWithError('Email ve şifre gerekli', $email, $redirect);
            return;
        }
        
        // Kullanıcıyı kontrol et
        $user = sql_one("SELECT * FROM users WHERE email = ?", [$email]);
        
        if (!$user || !password_verify($password, $user['password'])) {
            logger()->warning("Failed login attempt", [
                'email' => $email,
                'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
            ]);
            
            $this->showLoginWithError('Email veya şifre hatalı', $email, $redirect);
            return;
        }
        
        // Session oluştur
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_role'] = $user['role'];
        $_SESSION['logged_in'] = true;
        
        // Remember me cookie (basit versiyon)
        if ($remember) {
            // 30 gün cookie
            setcookie('remember_user', $user['id'], time() + (30 * 24 * 60 * 60), '/');
        }
        
        logger()->info("User logged in", [
            'user_id' => $user['id'],
            'email' => $user['email'],
            'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
            'redirect_to' => $redirect
        ]);
        
        // Güvenli redirect
        if (filter_var($redirect, FILTER_VALIDATE_URL) === false && str_starts_with($redirect, '/')) {
            redirect($redirect);
        } else {
            redirect('/dashboard');
        }
    }
    
    public function logout(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        $userId = $_SESSION['user_id'] ?? null;
        
        // Session temizle
        $_SESSION = array();
        session_destroy();
        
        // Cookie temizle
        if (isset($_COOKIE['remember_user'])) {
            setcookie('remember_user', '', time() - 3600, '/');
        }
        
        if ($userId) {
            logger()->info("User logged out", ['user_id' => $userId]);
        }
        
        redirect('/auth/login');
    }
    
    private function showLoginWithError(string $error, string $email = '', string $redirect = '/dashboard'): void
    {
        echo '<!DOCTYPE html>
<html>
<head>
    <title>Giriş Yap - koruPHP</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 400px; margin: 50px auto; padding: 20px; background: #f5f5f5; }
        .login-container { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input[type="email"], input[type="password"] { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box; }
        .remember-me { margin: 15px 0; }
        button { width: 100%; padding: 12px; background: #007cba; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 16px; }
        button:hover { background: #005a87; }
        .error { color: #dc3545; margin-bottom: 15px; padding: 10px; background: #f8d7da; border-radius: 4px; }
        .links { text-align: center; margin-top: 20px; }
        .links a { color: #007cba; text-decoration: none; }
    </style>
</head>
<body>
    <div class="login-container">
        <h1>koruPHP - Giriş Yap</h1>
        
        <div class="error">' . htmlspecialchars($error) . '</div>
        
        <form method="POST" action="/auth/login">
            <input type="hidden" name="redirect" value="' . htmlspecialchars($redirect) . '">
            
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" value="' . htmlspecialchars($email) . '" required autofocus>
            </div>
            
            <div class="form-group">
                <label for="password">Şifre:</label>
                <input type="password" id="password" name="password" required>
            </div>
            
            <div class="remember-me">
                <label>
                    <input type="checkbox" name="remember" value="1"> Beni Hatırla (30 gün)
                </label>
            </div>
            
            <button type="submit">Giriş Yap</button>
        </form>
        
        <div class="links">
            <a href="/test">Test Sayfasına Dön</a> |
            <a href="/test/create-test-users">Test Kullanıcıları Oluştur</a>
        </div>
    </div>
</body>
</html>';
    }
}