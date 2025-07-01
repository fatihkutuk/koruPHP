<?php

namespace App\Api\Controllers;

use Koru\Controller;

class AuthController extends Controller
{
    /**
     * API Login - POST /api/auth/login
     */
    public function login(): void
    {
        try {
            // JSON input al
            $input = json_decode(file_get_contents('php://input'), true);
            
            if (!$input || empty($input['email']) || empty($input['password'])) {
                $this->jsonResponse([
                    'success' => false,
                    'message' => 'Email ve şifre gerekli'
                ], 400);
                return;
            }
            
            // Kullanıcı doğrulama
            $user = sql_one("SELECT * FROM users WHERE email = ? AND status = 'active'", [$input['email']]);
            
            if (!$user || !password_verify($input['password'], $user['password'])) {
                $this->jsonResponse([
                    'success' => false,
                    'message' => 'Email veya şifre hatalı'
                ], 401);
                return;
            }
            
            // API token oluştur
            $token = $this->createApiToken($user['id']);
            
            $this->jsonResponse([
                'success' => true,
                'message' => 'Giriş başarılı',
                'data' => [
                    'token' => $token['token'],
                    'user' => [
                        'id' => $user['id'],
                        'name' => $user['name'],
                        'email' => $user['email'],
                        'role' => $user['role']
                    ],
                    'expires_at' => $token['expires_at']
                ]
            ]);
            
        } catch (\Exception $e) {
            logger()->error("API login error", ['error' => $e->getMessage()]);
            $this->jsonResponse([
                'success' => false,
                'message' => 'Sistem hatası'
            ], 500);
        }
    }
    
    /**
     * Current User - GET /api/auth/me
     */
    public function me(): void
    {
        try {
            $user = $this->getCurrentApiUser();
            
            if (!$user) {
                $this->jsonResponse([
                    'success' => false,
                    'message' => 'Kullanıcı bulunamadı'
                ], 404);
                return;
            }
            
            $this->jsonResponse([
                'success' => true,
                'data' => [
                    'id' => $user['id'],
                    'name' => $user['name'],
                    'email' => $user['email'],
                    'role' => $user['role'],
                    'status' => $user['status']
                ]
            ]);
            
        } catch (\Exception $e) {
            $this->jsonResponse([
                'success' => false,
                'message' => 'Sistem hatası'
            ], 500);
        }
    }
    
    /**
     * API Logout - POST /api/auth/logout
     */
    public function logout(): void
    {
        try {
            $token = $this->getApiTokenFromHeader();
            
            if ($token && $this->revokeApiToken($token)) {
                $this->jsonResponse([
                    'success' => true,
                    'message' => 'Çıkış başarılı'
                ]);
            } else {
                $this->jsonResponse([
                    'success' => false,
                    'message' => 'Token bulunamadı'
                ], 404);
            }
            
        } catch (\Exception $e) {
            $this->jsonResponse([
                'success' => false,
                'message' => 'Sistem hatası'
            ], 500);
        }
    }
    
    /**
     * Refresh Token - POST /api/auth/refresh
     */
    public function refresh(): void
    {
        try {
            $token = $this->getApiTokenFromHeader();
            
            if (!$token) {
                $this->jsonResponse([
                    'success' => false,
                    'message' => 'Token gerekli'
                ], 400);
                return;
            }
            
            $user = $this->validateApiToken($token);
            
            if (!$user) {
                $this->jsonResponse([
                    'success' => false,
                    'message' => 'Geçersiz token'
                ], 401);
                return;
            }
            
            // Eski token'ı iptal et ve yenisini oluştur
            $this->revokeApiToken($token);
            $newToken = $this->createApiToken($user['id']);
            
            $this->jsonResponse([
                'success' => true,
                'data' => [
                    'token' => $newToken['token'],
                    'expires_at' => $newToken['expires_at']
                ]
            ]);
            
        } catch (\Exception $e) {
            $this->jsonResponse([
                'success' => false,
                'message' => 'Token yenilenemedi'
            ], 500);
        }
    }
    
    /**
     * API Token oluştur
     */
    private function createApiToken(int $userId): array
    {
        $token = bin2hex(random_bytes(32));
        $hashedToken = hash('sha256', $token);
        $expiresAt = date('Y-m-d H:i:s', strtotime('+7 days'));
        
        sql_execute("
            INSERT INTO api_tokens (user_id, token, expires_at, created_at) 
            VALUES (?, ?, ?, ?)
        ", [$userId, $hashedToken, $expiresAt, date('Y-m-d H:i:s')]);
        
        return [
            'token' => $token,
            'expires_at' => $expiresAt
        ];
    }
    
    /**
     * API Token doğrula
     */
    private function validateApiToken(string $token): ?array
    {
        $hashedToken = hash('sha256', $token);
        
        $tokenRecord = sql_one("
            SELECT at.*, u.* 
            FROM api_tokens at 
            JOIN users u ON at.user_id = u.id 
            WHERE at.token = ? AND at.expires_at > NOW() AND u.status = 'active'
        ", [$hashedToken]);
        
        if ($tokenRecord) {
            // Last used güncelle
            sql_execute("
                UPDATE api_tokens 
                SET last_used_at = NOW() 
                WHERE token = ?
            ", [$hashedToken]);
        }
        
        return $tokenRecord;
    }
    
    /**
     * Header'dan token al
     */
    private function getApiTokenFromHeader(): ?string
    {
        $headers = getallheaders();
        $authorization = $headers['Authorization'] ?? $headers['authorization'] ?? null;
        
        if ($authorization && str_starts_with($authorization, 'Bearer ')) {
            return substr($authorization, 7);
        }
        
        return null;
    }
    
    /**
     * API Token iptal et
     */
    private function revokeApiToken(string $token): bool
    {
        $hashedToken = hash('sha256', $token);
        return sql_execute("DELETE FROM api_tokens WHERE token = ?", [$hashedToken]);
    }
    
    /**
     * Current API user
     */
    private function getCurrentApiUser(): ?array
    {
        $token = $this->getApiTokenFromHeader();
        return $token ? $this->validateApiToken($token) : null;
    }
    
    /**
     * JSON response helper
     */
    private function jsonResponse(array $data, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
    }
}