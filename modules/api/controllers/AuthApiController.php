<?php

namespace App\Api\Controllers;

use Koru\Controller;
use Koru\Auth\AuthManager;
use Koru\Auth\Providers\JwtAuthProvider;

class AuthApiController extends Controller
{
    private AuthManager $auth;
    private JwtAuthProvider $jwtProvider;
    
    public function __construct()
    {
        parent::__construct();
        $this->auth = new AuthManager();
        $this->jwtProvider = new JwtAuthProvider();
    }
    
    /**
     * API Login - JWT token döndür
     */
    public function login(): void
    {
        try {
            $input = json_decode(file_get_contents('php://input'), true) ?? [];
            
            $credentials = [
                'email' => $input['email'] ?? '',
                'password' => $input['password'] ?? ''
            ];
            
            if (empty($credentials['email']) || empty($credentials['password'])) {
                $this->json(['error' => 'Email and password required'], 400);
                return;
            }
            
            // JWT provider ile authentication
            $user = $this->jwtProvider->authenticate($credentials);
            
            if ($user) {
                // JWT token'ları oluştur
                $tokens = $this->jwtProvider->createTokens($user);
                
                logger()->info("User logged in via JWT API", [
                    'user_id' => $user->getId(),
                    'email' => $user->getEmail()
                ]);
                
                $this->json([
                    'success' => true,
                    'message' => 'Authentication successful',
                    'tokens' => $tokens,
                    'user' => $user->toArray()
                ]);
            } else {
                $this->json(['error' => 'Invalid credentials'], 401);
            }
            
        } catch (\Exception $e) {
            logger()->error("API login error", ['error' => $e->getMessage()]);
            $this->json(['error' => 'Authentication failed'], 500);
        }
    }
    
    /**
     * Token Refresh
     */
    public function refresh(): void
    {
        try {
            $input = json_decode(file_get_contents('php://input'), true) ?? [];
            $refreshToken = $input['refresh_token'] ?? '';
            
            if (empty($refreshToken)) {
                $this->json(['error' => 'Refresh token required'], 400);
                return;
            }
            
            $tokens = $this->jwtProvider->refreshToken($refreshToken);
            
            if ($tokens) {
                $this->json([
                    'success' => true,
                    'tokens' => $tokens
                ]);
            } else {
                $this->json(['error' => 'Invalid refresh token'], 401);
            }
            
        } catch (\Exception $e) {
            $this->json(['error' => 'Token refresh failed'], 500);
        }
    }
    
    /**
     * Token Validation
     */
    public function validate(): void
    {
        $authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
        
        if (strpos($authHeader, 'Bearer ') !== 0) {
            $this->json(['error' => 'Bearer token required'], 400);
            return;
        }
        
        $token = substr($authHeader, 7);
        $user = $this->jwtProvider->validateToken($token);
        
        if ($user) {
            $this->json([
                'valid' => true,
                'user' => $user->toArray()
            ]);
        } else {
            $this->json(['valid' => false], 401);
        }
    }
    
    /**
     * Logout
     */
    public function logout(): void
    {
        $authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
        
        if (strpos($authHeader, 'Bearer ') === 0) {
            $token = substr($authHeader, 7);
            $this->jwtProvider->logout($token);
        }
        
        $this->json(['success' => true, 'message' => 'Logged out successfully']);
    }
}