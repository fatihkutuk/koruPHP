<?php

namespace Koru\Auth\Providers;

use Koru\Auth\Interfaces\AuthProviderInterface;
use Koru\Auth\Interfaces\UserInterface;
use Koru\Auth\ExternalUser;

class ApiAuthProvider implements AuthProviderInterface
{
    private string $authApiUrl;
    private string $apiKey;
    private array $headers;
    
    public function __construct()
    {
        $this->authApiUrl = config('EXTERNAL_AUTH_URL', '');
        $this->apiKey = config('EXTERNAL_AUTH_API_KEY', '');
        $this->headers = [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->apiKey
        ];
    }
    
    public function authenticate(array $credentials): ?UserInterface
    {
        $response = $this->makeRequest('/auth/login', 'POST', $credentials);
        
        if ($response && isset($response['user'])) {
            return new ExternalUser($response['user']);
        }
        
        return null;
    }
    
    public function validateToken(string $token): ?UserInterface
    {
        $response = $this->makeRequest('/auth/validate', 'POST', ['token' => $token]);
        
        if ($response && isset($response['user'])) {
            return new ExternalUser($response['user']);
        }
        
        return null;
    }
    
    public function logout(string $token = null): bool
    {
        if ($token) {
            $response = $this->makeRequest('/auth/logout', 'POST', ['token' => $token]);
            return $response !== null;
        }
        
        return true;
    }
    
    public function refreshToken(string $refreshToken): ?array
    {
        $response = $this->makeRequest('/auth/refresh', 'POST', ['refresh_token' => $refreshToken]);
        
        return $response['tokens'] ?? null;
    }
    
    public function getName(): string
    {
        return 'api';
    }
    
    public function getCapabilities(): array
    {
        return [
            'external' => true,
            'stateless' => true,
            'refresh_tokens' => true,
            'cross_service' => true
        ];
    }
    
    /**
     * API çağrısı yap
     */
    private function makeRequest(string $endpoint, string $method = 'GET', array $data = []): ?array
    {
        $url = rtrim($this->authApiUrl, '/') . $endpoint;
        
        $context = [
            'http' => [
                'method' => $method,
                'header' => implode("\r\n", $this->headers),
                'timeout' => 10
            ]
        ];
        
        if (!empty($data) && in_array($method, ['POST', 'PUT', 'PATCH'])) {
            $context['http']['content'] = json_encode($data);
        }
        
        $response = @file_get_contents($url, false, stream_context_create($context));
        
        if ($response === false) {
            logger()->error("External auth API request failed", [
                'url' => $url,
                'method' => $method
            ]);
            return null;
        }
        
        return json_decode($response, true);
    }
}