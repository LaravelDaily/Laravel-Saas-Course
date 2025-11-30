<?php

namespace App\Enums;

enum RoleEnum: string
{
    case Admin = 'admin';
    case Collaborator = 'collaborator';

    public function isAdmin(): bool
    {
        return $this === self::Admin;
    }

    public function label(): string
    {
        return match ($this) {
            self::Admin => 'Administrator',
            self::Collaborator => 'Collaborator',
        };
    }
}
