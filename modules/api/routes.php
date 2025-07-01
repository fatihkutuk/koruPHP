<?php

// API Routes
$router = app()->getRouter();

// Public API routes
$router->post('/api/auth/login', function() {
    $controller = new \App\Api\Controllers\AuthController();
    $controller->login();
});

$router->post('/api/auth/refresh', function() {
    $controller = new \App\Api\Controllers\AuthController();
    $controller->refresh();
});

// Protected API routes (API token middleware ile)
$router->group(['middleware' => ['api.auth']], function($router) {
    // Auth endpoints
    $router->post('/api/auth/logout', function() {
        $controller = new \App\Api\Controllers\AuthController();
        $controller->logout();
    });
    
    $router->get('/api/auth/me', function() {
        $controller = new \App\Api\Controllers\AuthController();
        $controller->me();
    });
    
    // User endpoints
    $router->get('/api/users', function() {
        $controller = new \App\Api\Controllers\UserController();
        $controller->index();
    });
    
    $router->get('/api/users/{id}', function() {
        $controller = new \App\Api\Controllers\UserController();
        $controller->show();
    });
    
    $router->post('/api/users', function() {
        $controller = new \App\Api\Controllers\UserController();
        $controller->store();
    });
    
    $router->put('/api/users/{id}', function() {
        $controller = new \App\Api\Controllers\UserController();
        $controller->update();
    });
    
    $router->delete('/api/users/{id}', function() {
        $controller = new \App\Api\Controllers\UserController();
        $controller->delete();
    });
});

// Health check endpoint (public)
$router->get('/api/health', function() {
    header('Content-Type: application/json');
    echo json_encode([
        'status' => 'OK',
        'timestamp' => date('Y-m-d H:i:s'),
        'version' => '1.0.0',
        'framework' => 'koruPHP'
    ]);
});

// API Info endpoint (public)
$router->get('/api', function() {
    header('Content-Type: application/json');
    echo json_encode([
        'name' => 'koruPHP API',
        'version' => '1.0.0',
        'description' => 'RESTful API with JWT-like token authentication',
        'endpoints' => [
            'auth' => [
                'POST /api/auth/login' => 'Login and get token',
                'GET /api/auth/me' => 'Get current user info',
                'POST /api/auth/logout' => 'Logout and revoke token',
                'POST /api/auth/refresh' => 'Refresh token'
            ],
            'users' => [
                'GET /api/users' => 'List users (admin only)',
                'GET /api/users/{id}' => 'Get user by ID',
                'POST /api/users' => 'Create user (admin only)',
                'PUT /api/users/{id}' => 'Update user',
                'DELETE /api/users/{id}' => 'Delete user (admin only)'
            ]
        ],
        'authentication' => 'Bearer token required for protected endpoints'
    ], JSON_PRETTY_PRINT);
});