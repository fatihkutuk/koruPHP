<?php

namespace App\Web\Controllers;

use Koru\Controller;
use Koru\Exceptions\DatabaseException;

class TestController extends Controller
{
    public function index(): void
    {
        echo "<!DOCTYPE html>";
        echo "<html><head><title>koruPHP Test Sayfası</title>";
        echo "<style>
            body { font-family: Arial, sans-serif; max-width: 1200px; margin: 0 auto; padding: 20px; background: #f4f4f4; }
            .test-section { background: white; padding: 20px; margin: 20px 0; border-radius: 5px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
            .test-link { display: inline-block; background: #007cba; color: white; padding: 10px 15px; margin: 5px; text-decoration: none; border-radius: 3px; }
            .test-link:hover { background: #005a87; }
            .info { background: #e7f3ff; padding: 10px; border-left: 4px solid #007cba; margin: 10px 0; }
            .success { color: #28a745; } .error { color: #dc3545; } .warning { color: #ffc107; }
        </style></head><body>";
        
        echo "<h1>🚀 koruPHP Test Sayfası</h1>";
        
        // Auth durumu göster
        $authStatus = auth()->check() ? 'Giriş Yapılmış' : 'Giriş Yapılmamış';
        $authClass = auth()->check() ? 'success' : 'error';
        echo "<div class='info'><strong>Auth Durumu:</strong> <span class='{$authClass}'>{$authStatus}</span></div>";
        
        if (auth()->check()) {
            $user = user();
            echo "<div class='info'><strong>Kullanıcı:</strong> {$user->getName()} ({$user->getEmail()})</div>";
        }
        
        echo "<div class='test-section'>";
        echo "<h3>🔍 Temel Testler</h3>";
        echo "<a href='/test/database' class='test-link'>Database Test</a>";
        echo "<a href='/test/log' class='test-link'>Log Test</a>";
        echo "<a href='/test/error' class='test-link'>Error Test</a>";
        echo "<a href='/test/auth' class='test-link'>Auth Test</a>";
        echo "<a href='/test/permissions' class='test-link'>Permission Test</a>";
        echo "</div>";
        
        echo "<div class='test-section'>";
        echo "<h3>🔐 Auth Testleri</h3>";
        if (auth()->guest()) {
            echo "<a href='/auth/login' class='test-link'>Login</a>";
            echo "<a href='/test/create-test-users' class='test-link'>Test Kullanıcıları Oluştur</a>";
        } else {
            echo "<a href='/auth/logout' class='test-link'>Logout</a>";
            echo "<a href='/dashboard' class='test-link'>Dashboard</a>";
        }
        echo "</div>";
        
        echo "<div class='test-section'>";
        echo "<h3>🔬 API Testleri</h3>";
        echo "<a href='/api/auth/login' class='test-link' onclick='return false'>API Login (JSON)</a>";
        echo "<a href='/api/user' class='test-link'>API User Info</a>";
        echo "</div>";
        
        echo "<div class='test-section'>";
        echo "<h3>📊 Sistem Bilgileri</h3>";
        echo "<p><strong>PHP Version:</strong> " . PHP_VERSION . "</p>";
        echo "<p><strong>Environment:</strong> <span class='" . (\Koru\Environment::isProduction() ? 'error' : 'success') . "'>" . \Koru\Environment::get() . "</span></p>";
        echo "<p><strong>Debug Mode:</strong> <span class='" . (\Koru\Environment::isDebugging() ? 'success' : 'error') . "'>" . (\Koru\Environment::isDebugging() ? 'Aktif' : 'Pasif') . "</span></p>";
        echo "<p><strong>Database Host:</strong> " . config('DEFAULT_DB_HOST') . ":" . config('DEFAULT_DB_PORT') . "</p>";
        echo "<p><strong>Database Name:</strong> " . config('DEFAULT_DB_DATABASE') . "</p>";
        echo "</div>";
        
        echo "</body></html>";
    }
    
    public function databaseTest(): void
    {
        try {
            $connectionTest = sql("SELECT 1 as test_connection");
            $tableTest = sql("SHOW TABLES");
            $userCount = sql_one("SELECT COUNT(*) as count FROM users");
            
            logger()->info("Database test completed successfully", [
                'tables_count' => count($tableTest),
                'user_count' => $userCount['count'] ?? 0
            ]);
            
            $this->json([
                'success' => true,
                'message' => 'Veritabanı testleri başarılı',
                'connection' => $connectionTest,
                'tables' => $tableTest,
                'user_count' => $userCount,
                'timestamp' => date('Y-m-d H:i:s')
            ]);
            
        } catch (\Exception $e) {
            logger()->error("Database test failed", [
                'error' => $e->getMessage()
            ]);
            
            $this->json([
                'success' => false,
                'error' => $e->getMessage(),
                'timestamp' => date('Y-m-d H:i:s')
            ], 500);
        }
    }
    
    public function logTest(): void
    {
        try {
            logger()->emergency("🚨 Emergency test");
            logger()->alert("⚠️ Alert test");
            logger()->critical("💥 Critical test");
            logger()->error("❌ Error test");
            logger()->warning("⚠️ Warning test");
            logger()->notice("📢 Notice test");
            logger()->info("ℹ️ Info test");
            logger()->debug("🔍 Debug test");
            
            $this->json([
                'success' => true,
                'message' => 'Tüm log seviyeleri test edildi',
                'timestamp' => date('Y-m-d H:i:s')
            ]);
            
        } catch (\Exception $e) {
            $this->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    public function errorTest(): void
    {
        throw new \Exception("Bu bir test hatasıdır! Environment: " . \Koru\Environment::get());
    }
    
    public function authTest(): void
    {
        try {
            $authManager = auth();
            
            $testResults = [
                'is_authenticated' => $authManager->check(),
                'current_user' => $authManager->check() ? $authManager->user()->toArray() : null,
                'providers' => $authManager->getProviders(),
                'capabilities' => [],
                'session_info' => [
                    'session_started' => session_status() === PHP_SESSION_ACTIVE,
                    'session_id' => session_id(),
                    'session_data' => $_SESSION ?? []
                ]
            ];
            
            // Her provider'ın yeteneklerini test et
            foreach ($authManager->getProviders() as $provider) {
                try {
                    $testResults['capabilities'][$provider] = $authManager->getProviderCapabilities($provider);
                } catch (\Exception $e) {
                    $testResults['capabilities'][$provider] = ['error' => $e->getMessage()];
                }
            }
            
            // JWT test (eğer kuruluysa)
            $testResults['jwt_available'] = class_exists('\Firebase\JWT\JWT');
            
            // External auth test
            $testResults['external_auth'] = [
                'enabled' => config('EXTERNAL_AUTH_ENABLED', false),
                'url' => config('EXTERNAL_AUTH_URL', ''),
                'has_api_key' => !empty(config('EXTERNAL_AUTH_API_KEY'))
            ];
            
            $this->json([
                'success' => true,
                'auth_test_results' => $testResults,
                'timestamp' => date('Y-m-d H:i:s')
            ]);
            
        } catch (\Exception $e) {
            $this->json([
                'success' => false,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    }
    
    public function permissionTest(): void
    {
        if (!auth()->check()) {
            $this->json([
                'error' => 'User not authenticated',
                'login_url' => '/auth/login'
            ], 401);
            return;
        }
        
        $user = user();
        
        // Test permission'ları
        $permissions = [
            'users.view', 'users.edit', 'users.delete', 'users.create', 'users.ban',
            'scada.view', 'scada.control', 'scada.config',
            'reports.view', 'reports.export',
            'api.access', 'api.admin',
            'system.admin'
        ];
        
        $permissionResults = [];
        foreach ($permissions as $permission) {
            $permissionResults[$permission] = can($permission);
        }
        
        // Role testleri
        $roles = ['user', 'operator', 'admin'];
        $roleResults = [];
        foreach ($roles as $role) {
            $roleResults[$role] = hasRole($role);
        }
        
        // Database'den gerçek izinleri al
        $dbPermissions = sql("
            SELECT p.name, p.display_name, p.category
            FROM permissions p
            JOIN user_permissions up ON p.id = up.permission_id
            WHERE up.user_id = ?
        ", [$user->getId()]);
        
        $this->json([
            'success' => true,
            'user' => $user->toArray(),
            'permission_tests' => $permissionResults,
            'role_tests' => $roleResults,
            'database_permissions' => $dbPermissions,
            'timestamp' => date('Y-m-d H:i:s')
        ]);
    }
    
    public function createTestUsers(): void
    {
        try {
            // Test kullanıcıları oluştur (sadece yoksa)
            $testUsers = [
                [
                    'name' => 'Test Admin',
                    'email' => 'test.admin@koruphp.com',
                    'password' => 'password123',
                    'role' => 'admin'
                ],
                [
                    'name' => 'Test User',
                    'email' => 'test.user@koruphp.com',
                    'password' => 'password123',
                    'role' => 'user'
                ],
                [
                    'name' => 'Test Operator',
                    'email' => 'test.operator@koruphp.com',
                    'password' => 'password123',
                    'role' => 'operator'
                ]
            ];
            
            $createdUsers = [];
            
            foreach ($testUsers as $userData) {
                // Kullanıcı zaten var mı kontrol et
                $existingUser = sql_one("SELECT id FROM users WHERE email = ?", [$userData['email']]);
                
                if (!$existingUser) {
                    $hashedPassword = password_hash($userData['password'], PASSWORD_DEFAULT);
                    
                    $userId = db()->insertGetId("
                        INSERT INTO users (name, email, password, role, status, email_verified_at, created_at) 
                        VALUES (?, ?, ?, ?, 'active', NOW(), NOW())
                    ", [
                        $userData['name'],
                        $userData['email'],
                        $hashedPassword,
                        $userData['role']
                    ]);
                    
                    // Profil oluştur
                    sql_execute("
                        INSERT INTO profiles (user_id, bio) 
                        VALUES (?, ?)
                    ", [$userId, 'Test kullanıcısı - ' . $userData['role']]);
                    
                    // Admin'e tüm izinleri ver
                    if ($userData['role'] === 'admin') {
                        sql_execute("
                            INSERT INTO user_permissions (user_id, permission_id)
                            SELECT ?, id FROM permissions
                        ", [$userId]);
                    }
                    // Operator'e SCADA izinlerini ver
                    elseif ($userData['role'] === 'operator') {
                        sql_execute("
                            INSERT INTO user_permissions (user_id, permission_id)
                            SELECT ?, id FROM permissions WHERE category = 'scada'
                        ", [$userId]);
                    }
                    
                    $createdUsers[] = [
                        'id' => $userId,
                        'name' => $userData['name'],
                        'email' => $userData['email'],
                        'role' => $userData['role']
                    ];
                } else {
                    $createdUsers[] = [
                        'id' => $existingUser['id'],
                        'email' => $userData['email'],
                        'status' => 'already_exists'
                    ];
                }
            }
            
            $this->json([
                'success' => true,
                'message' => 'Test kullanıcıları oluşturuldu',
                'users' => $createdUsers,
                'login_info' => [
                    'admin' => 'test.admin@koruphp.com / password123',
                    'user' => 'test.user@koruphp.com / password123',
                    'operator' => 'test.operator@koruphp.com / password123'
                ]
            ]);
            
        } catch (\Exception $e) {
            $this->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }
}