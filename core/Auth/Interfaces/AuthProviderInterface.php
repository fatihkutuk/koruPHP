<?php

namespace Koru\Auth\Interfaces;

interface AuthProviderInterface
{
    /**
     * Kullanıcı kimlik doğrulaması
     */
    public function authenticate(array $credentials): ?UserInterface;
    
    /**
     * Token ile kullanıcı doğrulaması
     */
    public function validateToken(string $token): ?UserInterface;
    
    /**
     * Logout işlemi
     */
    public function logout(string $token = null): bool;
    
    /**
     * Token yenileme
     */
    public function refreshToken(string $refreshToken): ?array;
    
    /**
     * Provider adı
     */
    public function getName(): string;
    
    /**
     * Provider desteklenen özellikler
     */
    public function getCapabilities(): array;
}