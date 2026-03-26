<?php

declare(strict_types=1);

namespace App;

enum Role: string
{
    case Admin = 'admin';
    case User = 'user';

    /**
     * @return array<int, string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
