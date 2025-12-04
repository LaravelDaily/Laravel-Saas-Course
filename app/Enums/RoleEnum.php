<?php

namespace App\Enums;

enum RoleEnum: string
{
    case Admin = 'admin';
    case User = 'user';
    case Viewer = 'viewer';

    public function isAdmin(): bool
    {
        return $this === self::Admin;
    }

    public function label(): string
    {
        return match ($this) {
            self::Admin => 'Administrator',
            self::User => 'User',
            self::Viewer => 'Viewer',
        };
    }
}
