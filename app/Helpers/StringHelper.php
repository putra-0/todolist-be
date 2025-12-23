<?php

namespace App\Helpers;

class StringHelper
{
    public static function explodeCommaSeparated(?string $value): array
    {
        if (is_null($value) || $value === '') {
            return [];
        }

        return array_values(
            array_filter(
                array_map('trim', explode(',', $value))
            )
        );
    }
}
