<?php

namespace Koru\Auth\Guards;

use Koru\Auth\Interfaces\GuardInterface;
use Koru\Auth\Interfaces\UserInterface;
use Koru\Auth\AuthManager;

class WebGuard implements GuardInterface
{
    private AuthManager $auth;
    
    public function __construct(AuthManager $auth)
    {
        $this->auth = $auth;
    }
    
    public function check(): bool
    {
        return $this->auth->check();
    }
    
    public function guest(): bool
    {
        return $this->auth->guest();
    }
    
    public function user(): ?UserInterface
    {
        return $this->auth->user();
    }
    
    public function id(): string|int|null
    {
        return $this->auth->id();
    }
    
    public function attempt(array $credentials): bool
    {
        return $this->auth->attempt($credentials, 'session');
    }
    
    public function login(UserInterface $user): void
    {
        $this->auth->setUser($user);
    }
    
    public function logout(): void
    {
        $this->auth->logout('session');
    }
}