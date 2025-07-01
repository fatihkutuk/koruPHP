<?php

namespace Koru\Auth\Interfaces;

interface GuardInterface
{
    public function check(): bool;
    public function guest(): bool;
    public function user(): ?UserInterface;
    public function id(): string|int|null;
    public function attempt(array $credentials): bool;
    public function login(UserInterface $user): void;
    public function logout(): void;
}