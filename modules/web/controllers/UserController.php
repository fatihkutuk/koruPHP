<?php

namespace App\Web\Controllers;

use Koru\Controller;
use App\Web\Models\User;

class UserController extends Controller
{
    public function index(): void
    {
        try {
            // Kullanıcıları veritabanından çek
            $users = sql("
                SELECT u.*, p.bio, p.phone, p.avatar 
                FROM users u 
                LEFT JOIN profiles p ON u.id = p.user_id 
                ORDER BY u.created_at DESC
            ");
            
            $this->render('web.user.index', [
                'users' => $users,
                'title' => 'Kullanıcı Listesi'
            ]);
            
        } catch (\Exception $e) {
            echo "<h1>Kullanıcı Listesi</h1>";
            echo "<p>Veritabanı bağlantısı hatası: " . htmlspecialchars($e->getMessage()) . "</p>";
            echo "<p><a href='/test'>Test Sayfasına Dön</a></p>";
        }
    }
    
    public function show(int $id): void
    {
        try {
            $user = sql_one("
                SELECT u.*, p.bio, p.phone, p.avatar 
                FROM users u 
                LEFT JOIN profiles p ON u.id = p.user_id 
                WHERE u.id = ?
            ", [$id]);
            
            if (!$user) {
                http_response_code(404);
                echo "<h1>Kullanıcı Bulunamadı</h1>";
                echo "<p>ID: {$id} kullanıcısı bulunamadı.</p>";
                echo "<p><a href='/users'>Kullanıcı Listesine Dön</a></p>";
                return;
            }
            
            $this->render('web.user.show', [
                'user' => $user,
                'title' => 'Kullanıcı Detayı: ' . $user['name']
            ]);
            
        } catch (\Exception $e) {
            echo "<h1>Hata</h1>";
            echo "<p>Kullanıcı bilgileri alınırken hata: " . htmlspecialchars($e->getMessage()) . "</p>";
        }
    }
    
    public function scadaPanel(): void
    {
        try {
            // SCADA sensör verilerini çek
            $sensorData = sql("
                SELECT * FROM sensor_data 
                ORDER BY created_at DESC 
                LIMIT 10
            ");
            
            // Anlık istatistikler
            $stats = [
                'total_sensors' => sql_one("SELECT COUNT(DISTINCT sensor_id) as count FROM sensor_data")['count'],
                'active_sensors' => sql_one("SELECT COUNT(DISTINCT sensor_id) as count FROM sensor_data WHERE created_at >= DATE_SUB(NOW(), INTERVAL 1 HOUR)")['count'],
                'critical_alerts' => sql_one("SELECT COUNT(*) as count FROM sensor_data WHERE status = 'critical' AND created_at >= DATE_SUB(NOW(), INTERVAL 1 HOUR)")['count'],
                'last_update' => sql_one("SELECT MAX(created_at) as last_update FROM sensor_data")['last_update']
            ];
            
            // Örnek sensör değerleri
            $gaugeData = [
                'temperature' => rand(20, 35),
                'pressure' => rand(100, 200), 
                'humidity' => rand(30, 80),
                'flow_rate' => rand(50, 150)
            ];
            
            $this->render('web.user.scada', [
                'sensorData' => $sensorData,
                'stats' => $stats,
                'gaugeData' => $gaugeData,
                'title' => 'SCADA Kontrol Paneli'
            ]);
            
        } catch (\Exception $e) {
            echo "<h1>SCADA Panel</h1>";
            echo "<p>SCADA verileri yüklenirken hata: " . htmlspecialchars($e->getMessage()) . "</p>";
            echo "<p><a href='/test'>Test Sayfasına Dön</a></p>";
        }
    }
}