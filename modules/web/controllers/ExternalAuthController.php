<?php

namespace App\Web\Controllers;

use Koru\Controller;
use Koru\Auth\AuthManager;

class ExternalAuthController extends Controller
{
    private AuthManager $auth;
    
    public function __construct()
    {
        parent::__construct();
        $this->auth = new AuthManager();
    }
    
    /**
     * External Node.js API ile authentication
     */
    public function loginWithNodeJS(): void
    {
        try {
            $input = json_decode(file_get_contents('php://input'), true) ?? [];
            
            // Node.js auth API'sına istek gönder
            $nodeAuthUrl = config('EXTERNAL_AUTH_URL') . '/login';
            $response = $this->makeExternalRequest($nodeAuthUrl, 'POST', [
                'email' => $input['email'] ?? '',
                'password' => $input['password'] ?? ''
            ]);
            
            if ($response && isset($response['success']) && $response['success']) {
                // External API'dan gelen kullanıcı bilgilerini kaydet
                $externalUser = $response['user'];
                
                // Local veritabanında kullanıcı var mı kontrol et
                $localUser = sql_one("
                    SELECT * FROM users 
                    WHERE external_id = ? AND external_provider = 'nodejs'
                ", [$externalUser['id']]);
                
                if (!$localUser) {
                    // Kullanıcıyı local'e kaydet
                    $userId = sql_execute("
                        INSERT INTO users (name, email, external_id, external_provider, status, created_at)
                        VALUES (?, ?, ?, 'nodejs', 'active', NOW())
                    ", [
                        $externalUser['name'],
                        $externalUser['email'],
                        $externalUser['id']
                    ]);
                    
                    // Profil oluştur
                    sql_execute("
                        INSERT INTO profiles (user_id, bio, metadata)
                        VALUES (?, ?, ?)
                    ", [
                        $userId,
                        'External user from Node.js',
                        json_encode($externalUser['metadata'] ?? [])
                    ]);
                    
                    $localUser = sql_one("SELECT * FROM users WHERE id = ?", [$userId]);
                }
                
                // Authentication başarılı
                $this->auth->setUser(new \Koru\Auth\User($localUser));
                
                $this->json([
                    'success' => true,
                    'message' => 'External authentication successful',
                    'user' => $this->auth->user()->toArray(),
                    'external_token' => $response['token'] ?? null
                ]);
                
            } else {
                $this->json([
                    'error' => 'External authentication failed',
                    'details' => $response['error'] ?? 'Unknown error'
                ], 401);
            }
            
        } catch (\Exception $e) {
            logger()->error("External auth error", [
                'error' => $e->getMessage(),
                'external_url' => config('EXTERNAL_AUTH_URL')
            ]);
            
            $this->json(['error' => 'External authentication service unavailable'], 503);
        }
    }
    
    /**
     * Mikroservis auth endpoint'i
     */
    public function microserviceAuth(): void
    {
        $serviceToken = $_SERVER['HTTP_X_SERVICE_TOKEN'] ?? '';
        $userToken = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
        
        if (empty($serviceToken)) {
            $this->json(['error' => 'Service token required'], 400);
            return;
        }
        
        // Service token doğrula
        $validServiceToken = config('MICROSERVICE_AUTH_TOKEN');
        if ($serviceToken !== $validServiceToken) {
            $this->json(['error' => 'Invalid service token'], 401);
            return;
        }
        
        // User token varsa doğrula
        if ($userToken && strpos($userToken, 'Bearer ') === 0) {
            $token = substr($userToken, 7);
            
            if ($this->auth->authenticateWithToken($token, 'jwt')) {
                $user = $this->auth->user();
                
                $this->json([
                    'authenticated' => true,
                    'user' => $user->toArray(),
                    'permissions' => $user->getPermissions(),
                    'roles' => $user->getRoles()
                ]);
                return;
            }
        }
        
        $this->json(['authenticated' => false], 401);
    }
    
    private function makeExternalRequest(string $url, string $method, array $data = []): ?array
    {
        $headers = [
            'Content-Type: application/json',
            'X-API-Key: ' . config('EXTERNAL_AUTH_API_KEY'),
            'User-Agent: koruPHP/1.0'
        ];
        
        $context = [
            'http' => [
                'method' => $method,
                'header' => implode("\r\n", $headers),
                'timeout' => 10
            ]
        ];
        
        if (!empty($data) && in_array($method, ['POST', 'PUT', 'PATCH'])) {
            $context['http']['content'] = json_encode($data);
        }
        
        $response = @file_get_contents($url, false, stream_context_create($context));
        
        if ($response === false) {
            throw new \Exception("External API request failed: {$url}");
        }
        
        return json_decode($response, true);
    }
}