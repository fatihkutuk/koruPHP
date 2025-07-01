<?php

namespace Koru\Auth\Interfaces;

interface PermissionProviderInterface
{
    /**
     * Kullanıcının izinlerini getir
     */
    public function getUserPermissions(string|int $userId): array;
    
    /**
     * Rol izinlerini getir
     */
    public function getRolePermissions(string $role): array;
    
    /**
     * İzin kontrolü
     */
    public function hasPermission(string|int $userId, string $permission): bool;
    
    /**
     * Rol kontrolü
     */
    public function hasRole(string|int $userId, string $role): bool;
    
    /**
     * İzinleri cache'le
     */
    public function cachePermissions(string|int $userId, array $permissions): void;
    
    /**
     * Cache'i temizle
     */
    public function clearCache(string|int $userId = null): void;
}