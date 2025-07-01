<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../core/Helpers.php';

use Koru\Application;

try {
    // Uygulamayı başlat
    global $app;
    $app = new Application();
    
    echo "🚀 koruPHP Database Setup\n";
    echo "========================\n\n";
    
    // Veritabanı bağlantısını test et
    if (!db()->isConnected()) {
        echo "❌ Veritabanı bağlantısı başarısız!\n";
        echo "Lütfen .env dosyasındaki veritabanı ayarlarını kontrol edin.\n";
        exit(1);
    }
    
    echo "✅ Veritabanı bağlantısı başarılı\n\n";
    
    // Users tablosu
    echo "📝 Users tablosu oluşturuluyor...\n";
    sql_execute("
        CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            email VARCHAR(255) UNIQUE NOT NULL,
            password VARCHAR(255) NOT NULL,
            role ENUM('admin', 'user', 'operator', 'viewer') DEFAULT 'user',
            status ENUM('active', 'inactive', 'banned') DEFAULT 'active',
            email_verified_at DATETIME NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "✅ Users tablosu hazır\n";
    
    // Profiles tablosu
    echo "📝 Profiles tablosu oluşturuluyor...\n";
    sql_execute("
        CREATE TABLE IF NOT EXISTS profiles (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            bio TEXT NULL,
            phone VARCHAR(20) NULL,
            avatar VARCHAR(255) NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "✅ Profiles tablosu hazır\n";
    
    // Permissions tablosu
    echo "📝 Permissions tablosu oluşturuluyor...\n";
    sql_execute("
        CREATE TABLE IF NOT EXISTS permissions (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100) UNIQUE NOT NULL,
            description TEXT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "✅ Permissions tablosu hazır\n";
    
    // User Permissions tablosu
    echo "📝 User Permissions tablosu oluşturuluyor...\n";
    sql_execute("
        CREATE TABLE IF NOT EXISTS user_permissions (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            permission_id INT NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
            FOREIGN KEY (permission_id) REFERENCES permissions(id) ON DELETE CASCADE,
            UNIQUE KEY unique_user_permission (user_id, permission_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "✅ User Permissions tablosu hazır\n";
    
    // API Tokens tablosu (YENİ)
    echo "📝 API Tokens tablosu oluşturuluyor...\n";
    sql_execute("
        CREATE TABLE IF NOT EXISTS api_tokens (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            token VARCHAR(255) UNIQUE NOT NULL,
            name VARCHAR(100) DEFAULT 'API Token',
            expires_at DATETIME NULL,
            last_used_at DATETIME NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "✅ API Tokens tablosu hazır\n";
    
    // Web Sessions tablosu (YENİ)
    echo "📝 Web Sessions tablosu oluşturuluyor...\n";
    sql_execute("
        CREATE TABLE IF NOT EXISTS web_sessions (
            id VARCHAR(128) PRIMARY KEY,
            user_id INT NOT NULL,
            remember_token VARCHAR(255) NULL,
            ip_address VARCHAR(45) NULL,
            user_agent TEXT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            expires_at DATETIME NULL,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "✅ Web Sessions tablosu hazır\n";
    
    // Temel izinleri ekle
    echo "📝 Temel izinler ekleniyor...\n";
    $permissions = [
        'view_users' => 'Kullanıcıları görüntüleme',
        'create_users' => 'Kullanıcı oluşturma',
        'edit_users' => 'Kullanıcı düzenleme',
        'delete_users' => 'Kullanıcı silme',
        'view_dashboard' => 'Dashboard görüntüleme',
        'view_reports' => 'Raporları görüntüleme',
        'manage_permissions' => 'İzin yönetimi',
        'system_admin' => 'Sistem yönetimi',
        'api_access' => 'API erişimi',
        'scada_view' => 'SCADA panel görüntüleme',
        'scada_control' => 'SCADA kontrol',
        'view_logs' => 'Log görüntüleme',
        'export_data' => 'Veri dışa aktarma'
    ];
    
    foreach ($permissions as $name => $description) {
        $existing = sql_one("SELECT id FROM permissions WHERE name = ?", [$name]);
        if (!$existing) {
            sql_execute("INSERT INTO permissions (name, description) VALUES (?, ?)", [$name, $description]);
        }
    }
    echo "✅ İzinler hazır\n";
    
    // Test kullanıcılarını ekle
    echo "📝 Test kullanıcıları kontrol ediliyor...\n";
    
    $testUsers = [
        [
            'name' => 'Test Admin',
            'email' => 'test.admin@koruphp.com',
            'password' => password_hash('password123', PASSWORD_DEFAULT),
            'role' => 'admin',
            'bio' => 'Test kullanıcısı - admin'
        ],
        [
            'name' => 'Test User',
            'email' => 'test.user@koruphp.com',
            'password' => password_hash('password123', PASSWORD_DEFAULT),
            'role' => 'user',
            'bio' => 'Test kullanıcısı - user'
        ],
        [
            'name' => 'Test Operator',
            'email' => 'test.operator@koruphp.com',
            'password' => password_hash('password123', PASSWORD_DEFAULT),
            'role' => 'operator',
            'bio' => 'Test kullanıcısı - operator'
        ]
    ];
    
    foreach ($testUsers as $userData) {
        $existing = sql_one("SELECT id FROM users WHERE email = ?", [$userData['email']]);
        
        if (!$existing) {
            // Kullanıcıyı ekle
            sql_execute("
                INSERT INTO users (name, email, password, role, status, email_verified_at, created_at) 
                VALUES (?, ?, ?, ?, 'active', NOW(), NOW())
            ", [$userData['name'], $userData['email'], $userData['password'], $userData['role']]);
            
            $userId = pdo()->lastInsertId();
            
            // Profil ekle
            sql_execute("
                INSERT INTO profiles (user_id, bio) 
                VALUES (?, ?)
            ", [$userId, $userData['bio']]);
            
            // Admin kullanıcısına tüm izinleri ver
            if ($userData['role'] === 'admin') {
                sql_execute("
                    INSERT INTO user_permissions (user_id, permission_id)
                    SELECT ?, id FROM permissions
                ", [$userId]);
            }
            
            echo "✅ {$userData['email']} kullanıcısı oluşturuldu\n";
        } else {
            echo "ℹ️  {$userData['email']} zaten mevcut\n";
        }
    }
    
    echo "\n🎉 Database setup tamamlandı!\n";
    echo "\nTest Kullanıcıları:\n";
    echo "Admin: test.admin@koruphp.com / password123\n";
    echo "User: test.user@koruphp.com / password123\n";
    echo "Operator: test.operator@koruphp.com / password123\n";
    echo "\nWeb Test: http://localhost:8000/test\n";
    echo "API Test: http://localhost:8000/test/api-demo\n";
    
} catch (\Exception $e) {
    echo "❌ Setup hatası: " . $e->getMessage() . "\n";
    echo "Dosya: " . $e->getFile() . ":" . $e->getLine() . "\n";
    exit(1);
}