<?php

namespace App\Enums;

enum UserRole: string
{
    case Admin = 'admin';
    case User = 'user';

    public static function toArray(array|self $exclude = []): array
    {
        $exclude = is_array($exclude) ? $exclude : [$exclude];

        $options = [];
        foreach (self::cases() as $case) {
            if (in_array($case, $exclude)) {
                continue;
            }

            $options[] = $case->value;
        }

        return $options;
    }
}
