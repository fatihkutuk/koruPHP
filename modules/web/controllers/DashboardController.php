<?php

namespace App\Web\Controllers;

use Koru\Controller;

class DashboardController extends Controller
{
    public function index(): void
    {
        // Session kontrolü
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!isset($_SESSION['user_id'])) {
            redirect('/auth/login');
            return;
        }
        
        $user = sql_one("SELECT * FROM users WHERE id = ?", [$_SESSION['user_id']]);
        
        if (!$user) {
            redirect('/auth/login');
            return;
        }
        
        // İstatistikler
        $stats = [
            'total_users' => sql_one("SELECT COUNT(*) as count FROM users")['count'] ?? 0,
            'active_users' => sql_one("SELECT COUNT(*) as count FROM users WHERE status = 'active'")['count'] ?? 0
        ];
        
        echo '<!DOCTYPE html>
<html>
<head>
    <title>Dashboard - koruPHP</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 0 auto; padding: 20px; }
        .header { background: #007cba; color: white; padding: 20px; border-radius: 5px; margin-bottom: 20px; }
        .stats { display: flex; gap: 20px; margin-bottom: 20px; }
        .stat-card { background: #f8f9fa; padding: 15px; border-radius: 5px; flex: 1; text-align: center; }
        .links { background: #e7f3ff; padding: 15px; border-radius: 5px; }
        .links a { display: inline-block; margin: 5px 10px 5px 0; color: #007cba; text-decoration: none; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Hoş Geldiniz, ' . htmlspecialchars($user['name']) . '!</h1>
        <p>Email: ' . htmlspecialchars($user['email']) . ' | Rol: ' . htmlspecialchars($user['role']) . '</p>
    </div>
    
    <div class="stats">
        <div class="stat-card">
            <h3>' . $stats['total_users'] . '</h3>
            <p>Toplam Kullanıcı</p>
        </div>
        <div class="stat-card">
            <h3>' . $stats['active_users'] . '</h3>
            <p>Aktif Kullanıcı</p>
        </div>
    </div>
    
    <div class="links">
        <h3>Hızlı Erişim</h3>
        <a href="/profile">Profil</a>
        <a href="/test">Test Sayfası</a>
        <a href="/test/permissions">İzin Testleri</a>';
        
        if ($user['role'] === 'admin') {
            echo '<a href="/admin">Admin Panel</a>';
        }
        
        echo '<a href="/auth/logout">Çıkış Yap</a>
    </div>
</body>
</html>';
    }
}