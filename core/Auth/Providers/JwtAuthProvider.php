<?php

namespace Koru\Auth\Providers;

use Koru\Auth\Interfaces\AuthProviderInterface;
use Koru\Auth\Interfaces\UserInterface;
use Koru\Auth\User;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JwtAuthProvider implements AuthProviderInterface
{
    private string $secret;
    private string $algorithm = 'HS256';
    private int $ttl = 3600; // 1 saat
    private int $refreshTtl = 86400; // 24 saat
    
    public function __construct()
    {
        $this->secret = config('JWT_SECRET', 'your-secret-key');
        $this->algorithm = config('JWT_ALGORITHM', 'HS256');
        $this->ttl = (int) config('JWT_TTL', 3600);
        $this->refreshTtl = (int) config('JWT_REFRESH_TTL', 86400);
    }
    
    public function authenticate(array $credentials): ?UserInterface
    {
        // Email/password ile kullanıcı bul
        $user = sql_one("
            SELECT * FROM users 
            WHERE email = ? AND status = 'active'
        ", [$credentials['email'] ?? '']);
        
        if ($user && password_verify($credentials['password'] ?? '', $user['password'])) {
            return new User($user);
        }
        
        return null;
    }
    
    public function validateToken(string $token): ?UserInterface
    {
        try {
            $decoded = JWT::decode($token, new Key($this->secret, $this->algorithm));
            $payload = (array) $decoded;
            
            // Token'ın geçerli olup olmadığını kontrol et
            if (isset($payload['exp']) && $payload['exp'] < time()) {
                return null;
            }
            
            // Kullanıcıyı veritabanından getir
            if (isset($payload['sub'])) {
                $user = sql_one("
                    SELECT * FROM users 
                    WHERE id = ? AND status = 'active'
                ", [$payload['sub']]);
                
                if ($user) {
                    return new User($user);
                }
            }
            
        } catch (\Exception $e) {
            logger()->warning("JWT validation failed", [
                'error' => $e->getMessage(),
                'token' => substr($token, 0, 20) . '...'
            ]);
        }
        
        return null;
    }
    
    public function logout(string $token = null): bool
    {
        if ($token) {
            // JWT token'ı blacklist'e ekle
            $this->blacklistToken($token);
        }
        
        return true;
    }
    
    public function refreshToken(string $refreshToken): ?array
    {
        try {
            $decoded = JWT::decode($refreshToken, new Key($this->secret, $this->algorithm));
            $payload = (array) $decoded;
            
            if (isset($payload['sub']) && isset($payload['type']) && $payload['type'] === 'refresh') {
                $user = sql_one("SELECT * FROM users WHERE id = ?", [$payload['sub']]);
                
                if ($user) {
                    return $this->createTokens(new User($user));
                }
            }
            
        } catch (\Exception $e) {
            logger()->warning("JWT refresh failed", ['error' => $e->getMessage()]);
        }
        
        return null;
    }
    
    public function getName(): string
    {
        return 'jwt';
    }
    
    public function getCapabilities(): array
    {
        return [
            'stateless' => true,
            'refresh_tokens' => true,
            'blacklist' => true,
            'cross_domain' => true
        ];
    }
    
    /**
     * JWT token'ları oluştur
     */
    public function createTokens(UserInterface $user): array
    {
        $now = time();
        
        // Access token
        $accessPayload = [
            'iss' => config('APP_URL'),
            'sub' => $user->getId(),
            'iat' => $now,
            'exp' => $now + $this->ttl,
            'type' => 'access',
            'user' => [
                'id' => $user->getId(),
                'email' => $user->getEmail(),
                'name' => $user->getName(),
                'roles' => $user->getRoles()
            ]
        ];
        
        // Refresh token
        $refreshPayload = [
            'iss' => config('APP_URL'),
            'sub' => $user->getId(),
            'iat' => $now,
            'exp' => $now + $this->refreshTtl,
            'type' => 'refresh'
        ];
        
        return [
            'access_token' => JWT::encode($accessPayload, $this->secret, $this->algorithm),
            'refresh_token' => JWT::encode($refreshPayload, $this->secret, $this->algorithm),
            'token_type' => 'bearer',
            'expires_in' => $this->ttl
        ];
    }
    
    private function blacklistToken(string $token): void
    {
        try {
            $decoded = JWT::decode($token, new Key($this->secret, $this->algorithm));
            $payload = (array) $decoded;
            
            if (isset($payload['exp'])) {
                sql_execute("
                    INSERT INTO jwt_blacklist (token_hash, expires_at) 
                    VALUES (?, ?)
                ", [hash('sha256', $token), date('Y-m-d H:i:s', $payload['exp'])]);
            }
        } catch (\Exception $e) {
            // Token decode edilemiyorsa zaten geçersiz
        }
    }
}