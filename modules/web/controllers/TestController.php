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
            .test-link.api { background: #28a745; }
            .test-link.api:hover { background: #1e7e34; }
            .info { background: #e7f3ff; padding: 10px; border-left: 4px solid #007cba; margin: 10px 0; }
            .success { color: #28a745; } .error { color: #dc3545; } .warning { color: #ffc107; }
            .api-demo { background: #f8f9fa; padding: 15px; margin: 10px 0; border-radius: 5px; }
            .code { background: #2d3748; color: #e2e8f0; padding: 10px; border-radius: 4px; font-family: monospace; margin: 5px 0; }
        </style></head><body>";
        
        echo "<h1>🚀 koruPHP Test & API Demo Sayfası</h1>";
        
        // Auth durumu göster
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        $isLoggedIn = isset($_SESSION['user_id']);
        $authStatus = $isLoggedIn ? 'Giriş Yapılmış' : 'Giriş Yapılmamış';
        $authClass = $isLoggedIn ? 'success' : 'error';
        echo "<div class='info'><strong>Web Auth Durumu:</strong> <span class='{$authClass}'>{$authStatus}</span></div>";
        
        if ($isLoggedIn) {
            $user = sql_one("SELECT * FROM users WHERE id = ?", [$_SESSION['user_id']]);
            if ($user) {
                echo "<div class='info'><strong>Kullanıcı:</strong> {$user['name']} ({$user['email']}) - Rol: {$user['role']}</div>";
            }
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
        echo "<h3>🔐 Web Auth Testleri</h3>";
        if (!$isLoggedIn) {
            echo "<a href='/auth/login' class='test-link'>Web Login</a>";
            echo "<a href='/test/create-test-users' class='test-link'>Test Kullanıcıları Oluştur</a>";
        } else {
            echo "<a href='/auth/logout' class='test-link'>Web Logout</a>";
            echo "<a href='/dashboard' class='test-link'>Dashboard</a>";
        }
        echo "</div>";
        
        // YENİ: API Test Bölümü
        echo "<div class='test-section'>";
        echo "<h3>🔬 API Testleri</h3>";
        echo "<a href='/test/api-demo' class='test-link api'>API Demo Sayfası</a>";
        echo "<a href='/test/api-login-test' class='test-link api'>API Login Test</a>";
        echo "<div class='api-demo'>";
        echo "<h4>📋 API Endpoints:</h4>";
        echo "<div class='code'>POST /api/auth/login - API Login</div>";
        echo "<div class='code'>GET /api/auth/me - Current User</div>";
        echo "<div class='code'>POST /api/auth/logout - API Logout</div>";
        echo "<div class='code'>GET /api/users - Users List (Admin)</div>";
        echo "<div class='code'>GET /api/users/{id} - User Detail</div>";
        echo "<div class='code'>POST /api/users - Create User (Admin)</div>";
        echo "</div>";
        echo "</div>";
        
        echo "<div class='test-section'>";
        echo "<h3>📊 Sistem Bilgileri</h3>";
        echo "<p><strong>PHP Version:</strong> " . PHP_VERSION . "</p>";
        echo "<p><strong>Framework:</strong> koruPHP</p>";
        echo "<p><strong>Database:</strong> " . (db()->isConnected() ? '✅ Bağlı' : '❌ Bağlı değil') . "</p>";
        echo "</div>";
        
        echo "</body></html>";
    }
    
    /**
     * API Demo Sayfası
     */
    public function apiDemo(): void
    {
        echo "<!DOCTYPE html>";
        echo "<html><head><title>API Demo - koruPHP</title>";
        echo "<style>
            body { font-family: Arial, sans-serif; max-width: 1000px; margin: 0 auto; padding: 20px; background: #f4f4f4; }
            .api-section { background: white; padding: 20px; margin: 20px 0; border-radius: 5px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
            .endpoint { background: #f8f9fa; padding: 15px; margin: 10px 0; border-radius: 5px; border-left: 4px solid #007cba; }
            .method { display: inline-block; padding: 4px 8px; border-radius: 3px; color: white; font-weight: bold; margin-right: 10px; }
            .method.post { background: #28a745; }
            .method.get { background: #007bff; }
            .method.delete { background: #dc3545; }
            .code { background: #2d3748; color: #e2e8f0; padding: 10px; border-radius: 4px; font-family: monospace; margin: 5px 0; overflow-x: auto; }
            .test-btn { background: #28a745; color: white; padding: 8px 15px; border: none; border-radius: 4px; cursor: pointer; margin: 5px; }
            .result { background: #f8f9fa; padding: 10px; margin: 10px 0; border-radius: 4px; white-space: pre-wrap; font-family: monospace; }
            .success { border-left: 4px solid #28a745; }
            .error { border-left: 4px solid #dc3545; }
            .form-group { margin: 10px 0; }
            .form-group label { display: block; margin-bottom: 5px; font-weight: bold; }
            .form-group input, .form-group textarea { width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; }
        </style>";
        echo "<script src='https://code.jquery.com/jquery-3.6.0.min.js'></script>";
        echo "</head><body>";
        
        echo "<h1>🔬 API Demo & Test Sayfası</h1>";
        echo "<p><a href='/test'>← Ana Test Sayfasına Dön</a></p>";
        
        // API Login Test
        echo "<div class='api-section'>";
        echo "<h3>🔐 API Login Test</h3>";
        echo "<div class='endpoint'>";
        echo "<span class='method post'>POST</span><strong>/api/auth/login</strong>";
        echo "</div>";
        
        echo "<div class='form-group'>";
        echo "<label>Email:</label>";
        echo "<input type='email' id='loginEmail' value='test.admin@koruphp.com' />";
        echo "</div>";
        
        echo "<div class='form-group'>";
        echo "<label>Password:</label>";
        echo "<input type='password' id='loginPassword' value='password123' />";
        echo "</div>";
        
        echo "<button class='test-btn' onclick='testApiLogin()'>API Login Test</button>";
        echo "<div id='loginResult' class='result' style='display:none;'></div>";
        echo "</div>";
        
        // Current User Test
        echo "<div class='api-section'>";
        echo "<h3>👤 Current User Test</h3>";
        echo "<div class='endpoint'>";
        echo "<span class='method get'>GET</span><strong>/api/auth/me</strong>";
        echo "</div>";
        echo "<button class='test-btn' onclick='testCurrentUser()'>Get Current User</button>";
        echo "<div id='currentUserResult' class='result' style='display:none;'></div>";
        echo "</div>";
        
        // Users List Test
        echo "<div class='api-section'>";
        echo "<h3>👥 Users List Test</h3>";
        echo "<div class='endpoint'>";
        echo "<span class='method get'>GET</span><strong>/api/users</strong>";
        echo "</div>";
        echo "<button class='test-btn' onclick='testUsersList()'>Get Users List</button>";
        echo "<div id='usersListResult' class='result' style='display:none;'></div>";
        echo "</div>";
        
        // Create User Test
        echo "<div class='api-section'>";
        echo "<h3>➕ Create User Test</h3>";
        echo "<div class='endpoint'>";
        echo "<span class='method post'>POST</span><strong>/api/users</strong>";
        echo "</div>";
        
        echo "<div class='form-group'>";
        echo "<label>Name:</label>";
        echo "<input type='text' id='newUserName' value='Test User " . time() . "' />";
        echo "</div>";
        
        echo "<div class='form-group'>";
        echo "<label>Email:</label>";
        echo "<input type='email' id='newUserEmail' value='test" . time() . "@example.com' />";
        echo "</div>";
        
        echo "<div class='form-group'>";
        echo "<label>Password:</label>";
        echo "<input type='password' id='newUserPassword' value='password123' />";
        echo "</div>";
        
        echo "<div class='form-group'>";
        echo "<label>Role:</label>";
        echo "<select id='newUserRole'>";
        echo "<option value='user'>User</option>";
        echo "<option value='admin'>Admin</option>";
        echo "</select>";
        echo "</div>";
        
        echo "<button class='test-btn' onclick='testCreateUser()'>Create User</button>";
        echo "<div id='createUserResult' class='result' style='display:none;'></div>";
        echo "</div>";
        
        // Token Storage
        echo "<div class='api-section'>";
        echo "<h3>🔑 Token Bilgisi</h3>";
        echo "<div id='tokenInfo' class='code'>Token yok - Önce login yapın</div>";
        echo "<button class='test-btn' onclick='clearToken()'>Token'ı Temizle</button>";
        echo "</div>";
        
        // JavaScript
        echo "<script>
        let apiToken = localStorage.getItem('apiToken') || '';
        
        function updateTokenDisplay() {
            const display = document.getElementById('tokenInfo');
            if (apiToken) {
                display.textContent = 'Token: ' + apiToken.substring(0, 20) + '...';
                display.className = 'code success';
            } else {
                display.textContent = 'Token yok - Önce login yapın';
                display.className = 'code error';
            }
        }
        
        function showResult(elementId, data, isSuccess = true) {
            const element = document.getElementById(elementId);
            element.style.display = 'block';
            element.textContent = JSON.stringify(data, null, 2);
            element.className = 'result ' + (isSuccess ? 'success' : 'error');
        }
        
        async function testApiLogin() {
            const email = document.getElementById('loginEmail').value;
            const password = document.getElementById('loginPassword').value;
            
            try {
                const response = await fetch('/api/auth/login', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ email, password })
                });
                
                const data = await response.json();
                
                if (data.success && data.data && data.data.token) {
                    apiToken = data.data.token;
                    localStorage.setItem('apiToken', apiToken);
                    updateTokenDisplay();
                }
                
                showResult('loginResult', data, data.success);
            } catch (error) {
                showResult('loginResult', { error: error.message }, false);
            }
        }
        
        async function testCurrentUser() {
            if (!apiToken) {
                showResult('currentUserResult', { error: 'Token gerekli - Önce login yapın' }, false);
                return;
            }
            
            try {
                const response = await fetch('/api/auth/me', {
                    headers: {
                        'Authorization': 'Bearer ' + apiToken
                    }
                });
                
                const data = await response.json();
                showResult('currentUserResult', data, data.success);
            } catch (error) {
                showResult('currentUserResult', { error: error.message }, false);
            }
        }
        
        async function testUsersList() {
            if (!apiToken) {
                showResult('usersListResult', { error: 'Token gerekli - Önce login yapın' }, false);
                return;
            }
            
            try {
                const response = await fetch('/api/users', {
                    headers: {
                        'Authorization': 'Bearer ' + apiToken
                    }
                });
                
                const data = await response.json();
                showResult('usersListResult', data, data.success);
            } catch (error) {
                showResult('usersListResult', { error: error.message }, false);
            }
        }
        
        async function testCreateUser() {
            if (!apiToken) {
                showResult('createUserResult', { error: 'Token gerekli - Önce login yapın' }, false);
                return;
            }
            
            const userData = {
                name: document.getElementById('newUserName').value,
                email: document.getElementById('newUserEmail').value,
                password: document.getElementById('newUserPassword').value,
                role: document.getElementById('newUserRole').value
            };
            
            try {
                const response = await fetch('/api/users', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': 'Bearer ' + apiToken
                    },
                    body: JSON.stringify(userData)
                });
                
                const data = await response.json();
                showResult('createUserResult', data, data.success);
            } catch (error) {
                showResult('createUserResult', { error: error.message }, false);
            }
        }
        
        function clearToken() {
            apiToken = '';
            localStorage.removeItem('apiToken');
            updateTokenDisplay();
        }
        
        // Sayfa yüklendiğinde token'ı göster
        updateTokenDisplay();
        </script>";
        
        echo "</body></html>";
    }
    
    /**
     * Basit API Login Test
     */
    public function apiLoginTest(): void
    {
        echo "<!DOCTYPE html>";
        echo "<html><head><title>API Login Test</title></head><body>";
        echo "<h1>🔐 API Login Test</h1>";
        
        // Test kullanıcısı ile login dene
        $testCredentials = [
            'email' => 'test.admin@koruphp.com',
            'password' => 'password123'
        ];
        
        echo "<h3>Test Credentials:</h3>";
        echo "<pre>" . json_encode($testCredentials, JSON_PRETTY_PRINT) . "</pre>";
        
        echo "<h3>cURL Komutu:</h3>";
        echo "<div style='background: #f4f4f4; padding: 10px; border-radius: 5px;'>";
        echo "<code>curl -X POST " . url('/api/auth/login') . " \\<br>";
        echo "&nbsp;&nbsp;-H \"Content-Type: application/json\" \\<br>";
        echo "&nbsp;&nbsp;-d '" . json_encode($testCredentials) . "'</code>";
        echo "</div>";
        
        echo "<p><a href='/test/api-demo'>→ API Demo Sayfasına Git</a></p>";
        echo "<p><a href='/test'>← Ana Test Sayfasına Dön</a></p>";
        
        echo "</body></html>";
    }
}