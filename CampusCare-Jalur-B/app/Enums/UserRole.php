<?php

namespace App\Enums;

enum UserRole: string
{
    case Admin = 'admin';
    case Petugas = 'petugas';
    case User = 'user';

    /**
     * Get the human readable label of the role.
     */
    public function label(): string
    {
        return match ($this) {
            self::Admin => 'Admin',
            self::Petugas => 'Petugas',
            self::User => 'User',
        };
    }
}
