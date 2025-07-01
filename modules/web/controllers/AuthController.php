<?php

namespace App\Web\Controllers;

use Koru\Controller;

class AuthController extends Controller
{
    public function showLogin(): void
    {
        // Zaten giriş yapmışsa dashboard'a yönlendir
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (isset($_SESSION['user_id'])) {
            redirect('/dashboard');
            return;
        }
        
        echo '<!DOCTYPE html>
<html>
<head>
    <title>Giriş Yap - koruPHP</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 400px; margin: 50px auto; padding: 20px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; }
        input[type="email"], input[type="password"] { width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; }
        button { width: 100%; padding: 10px; background: #007cba; color: white; border: none; border-radius: 4px; cursor: pointer; }
        button:hover { background: #005a87; }
        .error { color: red; margin-bottom: 15px; }
        .info { background: #e7f3ff; padding: 10px; border-left: 4px solid #007cba; margin-bottom: 20px; }
    </style>
</head>
<body>
    <h1>koruPHP - Giriş Yap</h1>
    
    <div class="info">
        <strong>Test Kullanıcıları:</strong><br>
        Admin: test.admin@koruphp.com / password123<br>
        User: test.user@koruphp.com / password123<br>
        Operator: test.operator@koruphp.com / password123
    </div>
    
    <form method="POST" action="/auth/login">
        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>
        </div>
        
        <div class="form-group">
            <label for="password">Şifre:</label>
            <input type="password" id="password" name="password" required>
        </div>
        
        <button type="submit">Giriş Yap</button>
    </form>
    
    <p><a href="/test">Test Sayfasına Dön</a></p>
</body>
</html>';
    }
    
    public function login(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        
        if (empty($email) || empty($password)) {
            echo "<h1>Hata</h1><p>Email ve şifre gerekli</p><a href='/auth/login'>Tekrar Dene</a>";
            return;
        }
        
        // Kullanıcıyı kontrol et
        $user = sql_one("SELECT * FROM users WHERE email = ? AND status = 'active'", [$email]);
        
        if ($user && password_verify($password, $user['password'])) {
            // Session'a kaydet
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_role'] = $user['role'];
            $_SESSION['login_time'] = time();
            
            // Son giriş bilgilerini güncelle
            sql_execute("UPDATE users SET last_login = NOW(), login_count = login_count + 1 WHERE id = ?", [$user['id']]);
            
            logger()->info("User logged in", [
                'user_id' => $user['id'],
                'email' => $user['email']
            ]);
            
            redirect('/dashboard');
        } else {
            echo "<h1>Giriş Başarısız</h1><p>Email veya şifre hatalı</p><a href='/auth/login'>Tekrar Dene</a>";
        }
    }
    
    public function logout(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        logger()->info("User logged out", [
            'user_id' => $_SESSION['user_id'] ?? null
        ]);
        
        // Session'ı temizle
        $_SESSION = [];
        session_destroy();
        
        redirect('/auth/login');
    }
}