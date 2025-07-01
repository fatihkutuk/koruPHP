<?php

namespace Koru\Auth\Interfaces;

interface TokenProviderInterface
{
    /**
     * Token oluştur
     */
    public function createToken(UserInterface $user, array $claims = []): array;
    
    /**
     * Token'ı doğrula
     */
    public function validateToken(string $token): ?array;
    
    /**
     * Token'ı yenile
     */
    public function refreshToken(string $refreshToken): ?array;
    
    /**
     * Token'ı iptal et
     */
    public function revokeToken(string $token): bool;
    
    /**
     * Token'ın süresini kontrol et
     */
    public function isExpired(string $token): bool;
}