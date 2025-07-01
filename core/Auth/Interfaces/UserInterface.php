<?php

namespace Koru\Auth\Interfaces;

interface UserInterface
{
    public function getId(): string|int;
    public function getEmail(): string;
    public function getName(): string;
    public function getRoles(): array;
    public function getPermissions(): array;
    public function hasRole(string $role): bool;
    public function hasPermission(string $permission): bool;
    public function isActive(): bool;
    public function getMetadata(): array;
    public function toArray(): array;
}