<?php

$router = app()->getRouter();

// Ana sayfa
$router->get('/', function() {
    redirect('/test');
});

// Test rotaları (herkese açık - middleware yok)
$router->get('/test', function() {
    $controller = new \App\Web\Controllers\TestController();
    $controller->index();
});

$router->get('/test/database', function() {
    $controller = new \App\Web\Controllers\TestController();
    $controller->databaseTest();
});

$router->get('/test/log', function() {
    $controller = new \App\Web\Controllers\TestController();
    $controller->logTest();
});

$router->get('/test/error', function() {
    $controller = new \App\Web\Controllers\TestController();
    $controller->errorTest();
});

$router->get('/test/auth', function() {
    $controller = new \App\Web\Controllers\TestController();
    $controller->authTest();
});

$router->get('/test/permissions', function() {
    $controller = new \App\Web\Controllers\TestController();
    $controller->permissionTest();
});

$router->get('/test/create-test-users', function() {
    $controller = new \App\Web\Controllers\TestController();
    $controller->createTestUsers();
});

// Auth rotaları
$router->get('/auth/login', function() {
    $controller = new \App\Web\Controllers\AuthController();
    $controller->showLogin();
});

$router->post('/auth/login', function() {
    $controller = new \App\Web\Controllers\AuthController();
    $controller->login();
});

$router->get('/auth/logout', function() {
    $controller = new \App\Web\Controllers\AuthController();
    $controller->logout();
});

// Dashboard (authentication gerekli)
$router->group(['middleware' => ['auth']], function($router) {
    $router->get('/dashboard', function() {
        $controller = new \App\Web\Controllers\DashboardController();
        $controller->index();
    });
    
    $router->get('/profile', function() {
        echo "<h1>Profile Page</h1><p>Bu sayfa authentication gerektirir.</p>";
        echo "<p><strong>Kullanıcı:</strong> " . ($_SESSION['user_id'] ?? 'Bilinmiyor') . "</p>";
        echo "<p><a href='/auth/logout'>Çıkış Yap</a></p>";
    });
});

// Admin rotaları
$router->group(['middleware' => ['auth', 'admin']], function($router) {
    $router->get('/admin', function() {
        echo "<h1>Admin Panel</h1><p>Bu sayfa admin yetkisi gerektirir.</p>";
        echo "<p><a href='/dashboard'>Dashboard</a> | <a href='/auth/logout'>Çıkış Yap</a></p>";
    });
});

// API rotaları (basit)
$router->get('/api/test', function() {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => true,
        'message' => 'API çalışıyor',
        'timestamp' => date('Y-m-d H:i:s')
    ]);
});

$router->get('/api/user', function() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    header('Content-Type: application/json');
    
    if (!isset($_SESSION['user_id'])) {
        http_response_code(401);
        echo json_encode(['error' => 'Authentication required']);
        return;
    }
    
    $user = sql_one("SELECT id, name, email, role FROM users WHERE id = ?", [$_SESSION['user_id']]);
    
    if ($user) {
        echo json_encode(['user' => $user]);
    } else {
        http_response_code(404);
        echo json_encode(['error' => 'User not found']);
    }
});