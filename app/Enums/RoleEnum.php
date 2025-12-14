<?php

namespace App\Enums;

enum RoleEnum: string
{
    case SuperAdmin = 'superadmin';
    case Admin = 'admin';
    case User = 'user';
    case Viewer = 'viewer';

    public function isAdmin(): bool
    {
        return $this === self::Admin;
    }

    public function isSuperAdmin(): bool
    {
        return $this === self::SuperAdmin;
    }

    public function label(): string
    {
        return match ($this) {
            self::SuperAdmin => 'Super Administrator',
            self::Admin => 'Administrator',
            self::User => 'User',
            self::Viewer => 'Viewer',
        };
    }
}
