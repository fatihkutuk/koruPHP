<?php

namespace App\Middleware;

class ApiAuthMiddleware
{
    public function handle(): bool
    {
        try {
            $headers = getallheaders();
            $authorization = $headers['Authorization'] ?? $headers['authorization'] ?? null;
            
            if (!$authorization || !str_starts_with($authorization, 'Bearer ')) {
                $this->handleUnauthorized('Authorization header gerekli');
                return false;
            }
            
            $token = substr($authorization, 7);
            $hashedToken = hash('sha256', $token);
            
            $user = sql_one("
                SELECT u.* 
                FROM api_tokens at 
                JOIN users u ON at.user_id = u.id 
                WHERE at.token = ? AND at.expires_at > NOW() AND u.status = 'active'
            ", [$hashedToken]);
            
            if (!$user) {
                $this->handleUnauthorized('Geçersiz veya süresi dolmuş token');
                return false;
            }
            
            // Last used güncelle
            sql_execute("UPDATE api_tokens SET last_used_at = NOW() WHERE token = ?", [$hashedToken]);
            
            logger()->debug("API Auth successful", [
                'user_id' => $user['id'],
                'email' => $user['email']
            ]);
            
            return true;
            
        } catch (\Exception $e) {
            logger()->error("API Auth error", ['error' => $e->getMessage()]);
            $this->handleUnauthorized('Authentication failed');
            return false;
        }
    }
    
    private function handleUnauthorized(string $message): void
    {
        http_response_code(401);
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'message' => $message
        ], JSON_UNESCAPED_UNICODE);
    }
}