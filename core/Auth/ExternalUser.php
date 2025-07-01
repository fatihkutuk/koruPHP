<?php

namespace Koru\Auth;

use Koru\Auth\Interfaces\UserInterface;

class ExternalUser implements UserInterface
{
    private array $attributes;
    
    public function __construct(array $attributes)
    {
        $this->attributes = $attributes;
    }
    
    public function getId(): string|int
    {
        return $this->attributes['id'] ?? $this->attributes['user_id'];
    }
    
    public function getEmail(): string
    {
        return $this->attributes['email'];
    }
    
    public function getName(): string
    {
        return $this->attributes['name'] ?? $this->attributes['username'];
    }
    
    public function getRoles(): array
    {
        return $this->attributes['roles'] ?? [];
    }
    
    public function getPermissions(): array
    {
        return $this->attributes['permissions'] ?? [];
    }
    
    public function hasRole(string $role): bool
    {
        return in_array($role, $this->getRoles());
    }
    
    public function hasPermission(string $permission): bool
    {
        return in_array($permission, $this->getPermissions());
    }
    
    public function isActive(): bool
    {
        return ($this->attributes['status'] ?? 'active') === 'active';
    }
    
    public function getMetadata(): array
    {
        return $this->attributes['metadata'] ?? [];
    }
    
    public function toArray(): array
    {
        return $this->attributes;
    }
}