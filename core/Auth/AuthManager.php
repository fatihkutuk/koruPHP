<?php

namespace Koru\Auth;

use Koru\Auth\Interfaces\AuthProviderInterface;
use Koru\Auth\Interfaces\UserInterface;
use Koru\Auth\Interfaces\PermissionProviderInterface;
use Koru\Auth\Providers\SessionAuthProvider;
use Koru\Auth\Providers\JwtAuthProvider;
use Koru\Auth\Providers\ApiAuthProvider;
class AuthManager
{
    private array $providers = [];
    private ?UserInterface $user = null;
    private string $defaultProvider = 'session';
    private ?PermissionProviderInterface $permissionProvider = null;
    private static ?self $instance = null;
    
    public function __construct()
    {
        $this->loadConfiguration();
        $this->registerDefaultProviders();
        $this->loadUserFromSession();
        
        // Singleton pattern için
        self::$instance = $this;
    }
    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    private function loadUserFromSession(): void
    {
        // Session'dan kullanıcıyı otomatik yükle
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (isset($_SESSION['user_id'])) {
            try {
                $user = sql_one("
                    SELECT u.*, p.bio, p.phone, p.avatar 
                    FROM users u 
                    LEFT JOIN profiles p ON u.id = p.user_id 
                    WHERE u.id = ? AND u.status = 'active'
                ", [$_SESSION['user_id']]);
                
                if ($user) {
                    $this->user = new User($user);
                }
            } catch (\Exception $e) {
                logger()->error("Failed to load user from session", [
                    'user_id' => $_SESSION['user_id'],
                    'error' => $e->getMessage()
                ]);
            }
        }
    }

    /**
     * Auth provider kaydet
     */
    public function registerProvider(string $name, AuthProviderInterface $provider): void
    {
        $this->providers[$name] = $provider;
    }
    
    /**
     * Guard kaydet
     */
    public function registerGuard(string $name, callable $guardFactory): void
    {
        $this->guards[$name] = $guardFactory;
    }
    
    /**
     * Permission provider ayarla
     */
    public function setPermissionProvider(PermissionProviderInterface $provider): void
    {
        $this->permissionProvider = $provider;
    }
    
    /**
     * Belirtilen provider ile authentication
     */
    public function via(string $provider = null): AuthProviderInterface
    {
        $providerName = $provider ?? $this->defaultProvider;
        
        if (!isset($this->providers[$providerName])) {
            throw new \Exception("Auth provider '{$providerName}' not found");
        }
        
        return $this->providers[$providerName];
    }
    
    /**
     * Belirtilen guard ile korunma
     */
    public function guard(string $guard = null): GuardInterface
    {
        $guardName = $guard ?? $this->defaultGuard;
        
        if (!isset($this->guards[$guardName])) {
            throw new \Exception("Auth guard '{$guardName}' not found");
        }
        
        return $this->guards[$guardName]();
    }
    
    /**
     * Kimlik doğrulama dene
     */
    public function attempt(array $credentials, string $provider = null): bool
    {
        $authProvider = $this->via($provider);
        $user = $authProvider->authenticate($credentials);
        
        if ($user) {
            $this->setUser($user);
            return true;
        }
        
        return false;
    }
    
    /**
     * Token ile doğrulama
     */
    public function authenticateWithToken(string $token, string $provider = null): bool
    {
        $authProvider = $this->via($provider);
        $user = $authProvider->validateToken($token);
        
        if ($user) {
            $this->setUser($user);
            return true;
        }
        
        return false;
    }
    
    /**
     * External API ile doğrulama
     */
    public function authenticateWithExternalApi(string $apiUrl, array $headers = []): bool
    {
        $response = $this->makeApiRequest($apiUrl, $headers);
        
        if ($response && isset($response['user'])) {
            $user = new ExternalUser($response['user']);
            $this->setUser($user);
            return true;
        }
        
        return false;
    }
    
    /**
     * Mevcut kullanıcıyı getir
     */
    public function user(): ?UserInterface
    {
        return $this->user;
    }
    
    /**
     * Kullanıcı girişi yapılmış mı?
     */
    public function check(): bool
    {
        return $this->user !== null;
    }
    
    /**
     * Misafir kullanıcı mı?
     */
    public function guest(): bool
    {
        return !$this->check();
    }
    
    /**
     * Kullanıcı ID
     */
    public function id(): string|int|null
    {
        return $this->user?->getId();
    }
    
    /**
     * İzin kontrolü
     */
    public function can(string $permission): bool
    {
        if (!$this->check()) {
            return false;
        }
        
        // Önce kullanıcının kendi izinlerini kontrol et
        if ($this->user->hasPermission($permission)) {
            return true;
        }
        
        // Permission provider varsa onu kullan
        if ($this->permissionProvider) {
            return $this->permissionProvider->hasPermission($this->id(), $permission);
        }
        
        return false;
    }
    
    /**
     * Rol kontrolü
     */
    public function hasRole(string $role): bool
    {
        if (!$this->check()) {
            return false;
        }
        
        if ($this->user->hasRole($role)) {
            return true;
        }
        
        if ($this->permissionProvider) {
            return $this->permissionProvider->hasRole($this->id(), $role);
        }
        
        return false;
    }
    
    /**
     * Çıkış yap
     */
    public function logout(string $provider = null): bool
    {
        $result = $this->via($provider)->logout();
        $this->user = null;
        return $result;
    }
    
    /**
     * Kullanıcıyı manuel olarak ayarla
     */
    public function setUser(UserInterface $user): void
    {
        $this->user = $user;
        
        // Permission cache'i yükle
        if ($this->permissionProvider) {
            $permissions = $this->permissionProvider->getUserPermissions($user->getId());
            $this->permissionProvider->cachePermissions($user->getId(), $permissions);
        }
    }
    
    /**
     * Konfigürasyonu yükle
     */
    private function loadConfiguration(): void
    {
        $this->defaultProvider = config('AUTH_DEFAULT_PROVIDER', 'session');
        $this->defaultGuard = config('AUTH_DEFAULT_GUARD', 'web');
    }
    
    /**
     * Varsayılan provider'ları kaydet
     */
    private function registerDefaultProviders(): void
    {
        $this->registerProvider('session', new SessionAuthProvider());
        
        // JWT sadece kuruluysa kaydet
        if (class_exists('\Firebase\JWT\JWT')) {
            $this->registerProvider('jwt', new JwtAuthProvider());
        }
        
        $this->registerProvider('api', new ApiAuthProvider());
    }
    /**
     * External API çağrısı
     */
    private function makeApiRequest(string $url, array $headers): ?array
    {
        $context = stream_context_create([
            'http' => [
                'method' => 'GET',
                'header' => implode("\r\n", $headers),
                'timeout' => 10
            ]
        ]);
        
        $response = file_get_contents($url, false, $context);
        
        if ($response === false) {
            return null;
        }
        
        return json_decode($response, true);
    }
    
    /**
     * Tüm provider'ları listele
     */
    public function getProviders(): array
    {
        return array_keys($this->providers);
    }
    
    /**
     * Provider yeteneklerini getir
     */
    public function getProviderCapabilities(string $provider): array
    {
        return $this->via($provider)->getCapabilities();
    }
}