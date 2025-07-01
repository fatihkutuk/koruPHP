<?php

namespace Koru\Auth;

use Koru\Auth\Interfaces\UserInterface;

class User implements UserInterface
{
    private array $attributes;
    private array $permissions = [];
    private array $roles = [];
    
    public function __construct(array $attributes)
    {
        $this->attributes = $attributes;
        $this->loadPermissions();
        $this->loadRoles();
    }
    
    public function getId(): string|int
    {
        return $this->attributes['id'];
    }
    
    public function getEmail(): string
    {
        return $this->attributes['email'];
    }
    
    public function getName(): string
    {
        return $this->attributes['name'];
    }
    
    public function getRoles(): array
    {
        return $this->roles;
    }
    
    public function getPermissions(): array
    {
        return $this->permissions;
    }
    
    public function hasRole(string $role): bool
    {
        return in_array($role, $this->roles) || in_array('admin', $this->roles);
    }
    
    public function hasPermission(string $permission): bool
    {
        return in_array($permission, $this->permissions) || $this->hasRole('admin');
    }
    
    public function isActive(): bool
    {
        return ($this->attributes['status'] ?? 'active') === 'active';
    }
    
    public function getMetadata(): array
    {
        return [
            'last_login' => $this->attributes['last_login'] ?? null,
            'login_count' => $this->attributes['login_count'] ?? 0,
            'created_at' => $this->attributes['created_at'] ?? null,
            'avatar' => $this->attributes['avatar'] ?? 'default.jpg'
        ];
    }
    
    public function toArray(): array
    {
        return [
            'id' => $this->getId(),
            'email' => $this->getEmail(),
            'name' => $this->getName(),
            'roles' => $this->getRoles(),
            'permissions' => $this->getPermissions(),
            'metadata' => $this->getMetadata()
        ];
    }
    
    private function loadPermissions(): void
    {
        $permissions = sql("
            SELECT p.name 
            FROM permissions p
            JOIN user_permissions up ON p.id = up.permission_id
            WHERE up.user_id = ?
        ", [$this->getId()]);
        
        $this->permissions = array_column($permissions, 'name');
    }
    
    private function loadRoles(): void
    {
        $this->roles = explode(',', $this->attributes['role'] ?? 'user');
    }
}