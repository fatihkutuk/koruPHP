<?php

namespace App\Api\Controllers;

use Koru\Controller;

class UserController extends Controller
{
    /**
     * List Users - GET /api/users
     */
    public function index(): void
    {
        try {
            $currentUser = $this->getCurrentApiUser();
            
            // Sadece admin listeleyebilir
            if ($currentUser['role'] !== 'admin') {
                $this->jsonResponse([
                    'success' => false,
                    'message' => 'Yetkisiz erişim'
                ], 403);
                return;
            }
            
            $users = sql("
                SELECT id, name, email, role, status, created_at 
                FROM users 
                ORDER BY created_at DESC
            ");
            
            $this->jsonResponse([
                'success' => true,
                'data' => $users
            ]);
            
        } catch (\Exception $e) {
            $this->jsonResponse([
                'success' => false,
                'message' => 'Kullanıcılar listelenemedi'
            ], 500);
        }
    }
    
    /**
     * Show User - GET /api/users/{id}
     */
    public function show(): void
    {
        try {
            $id = $this->getRouteParam('id');
            $currentUser = $this->getCurrentApiUser();
            
            // Kullanıcı sadece kendi bilgisini veya admin tüm kullanıcıları görebilir
            if ($currentUser['id'] != $id && $currentUser['role'] !== 'admin') {
                $this->jsonResponse([
                    'success' => false,
                    'message' => 'Yetkisiz erişim'
                ], 403);
                return;
            }
            
            $user = sql_one("
                SELECT u.id, u.name, u.email, u.role, u.status, u.created_at,
                       p.bio, p.phone, p.avatar
                FROM users u 
                LEFT JOIN profiles p ON u.id = p.user_id 
                WHERE u.id = ?
            ", [$id]);
            
            if (!$user) {
                $this->jsonResponse([
                    'success' => false,
                    'message' => 'Kullanıcı bulunamadı'
                ], 404);
                return;
            }
            
            $this->jsonResponse([
                'success' => true,
                'data' => $user
            ]);
            
        } catch (\Exception $e) {
            $this->jsonResponse([
                'success' => false,
                'message' => 'Kullanıcı bilgisi alınamadı'
            ], 500);
        }
    }
    
    /**
     * Create User - POST /api/users
     */
    public function store(): void
    {
        try {
            $currentUser = $this->getCurrentApiUser();
            
            // Sadece admin kullanıcı ekleyebilir
            if ($currentUser['role'] !== 'admin') {
                $this->jsonResponse([
                    'success' => false,
                    'message' => 'Yetkisiz erişim'
                ], 403);
                return;
            }
            
            $input = json_decode(file_get_contents('php://input'), true);
            
            // Validation
            $required = ['name', 'email', 'password'];
            foreach ($required as $field) {
                if (empty($input[$field])) {
                    $this->jsonResponse([
                        'success' => false,
                        'message' => "{$field} alanı gerekli"
                    ], 400);
                    return;
                }
            }
            
            // Email kontrolü
            $existingUser = sql_one("SELECT id FROM users WHERE email = ?", [$input['email']]);
            if ($existingUser) {
                $this->jsonResponse([
                    'success' => false,
                    'message' => 'Bu email zaten kullanılıyor'
                ], 409);
                return;
            }
            
            // Kullanıcı oluştur
            $hashedPassword = password_hash($input['password'], PASSWORD_DEFAULT);
            
            sql_execute("
                INSERT INTO users (name, email, password, role, status, created_at) 
                VALUES (?, ?, ?, ?, 'active', NOW())
            ", [
                $input['name'],
                $input['email'],
                $hashedPassword,
                $input['role'] ?? 'user'
            ]);
            
            $userId = pdo()->lastInsertId();
            
            $newUser = sql_one("
                SELECT id, name, email, role, status, created_at 
                FROM users WHERE id = ?
            ", [$userId]);
            
            $this->jsonResponse([
                'success' => true,
                'message' => 'Kullanıcı başarıyla oluşturuldu',
                'data' => $newUser
            ], 201);
            
        } catch (\Exception $e) {
            $this->jsonResponse([
                'success' => false,
                'message' => 'Kullanıcı oluşturulamadı'
            ], 500);
        }
    }
    
    // Helper methods
    private function getCurrentApiUser(): ?array
    {
        $headers = getallheaders();
        $authorization = $headers['Authorization'] ?? $headers['authorization'] ?? null;
        
        if (!$authorization || !str_starts_with($authorization, 'Bearer ')) {
            return null;
        }
        
        $token = substr($authorization, 7);
        $hashedToken = hash('sha256', $token);
        
        return sql_one("
            SELECT u.* 
            FROM api_tokens at 
            JOIN users u ON at.user_id = u.id 
            WHERE at.token = ? AND at.expires_at > NOW() AND u.status = 'active'
        ", [$hashedToken]);
    }
    
    private function getRouteParam(string $key): mixed
    {
        // Route parametrelerini almak için basit bir sistem
        $uri = $_SERVER['REQUEST_URI'];
        $parts = explode('/', trim($uri, '/'));
        
        // /api/users/{id} formatında son parça id'dir
        if ($key === 'id' && count($parts) >= 3) {
            return end($parts);
        }
        
        return null;
    }
    
    private function jsonResponse(array $data, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
    }
}