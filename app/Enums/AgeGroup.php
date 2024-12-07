<?php

namespace App\Enums;

enum AgeGroup: string
{
    case ALL = 'all';
    case CHILDREN = '0-6';
    case KIDS = '7-12';
    case TEENS = '13-20';
    case ADULTS = '21+';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function labels(): array
    {
        return [
            self::ALL->value => 'Todas las edades',
            self::CHILDREN->value => '0-6 años',
            self::KIDS->value => '7-12 años',
            self::TEENS->value => '13-20 años',
            self::ADULTS->value => '21+ años'
        ];
    }

    public static function toDatabase(?string $value): ?string
    {
        return $value === 'all' ? null : $value;
    }

    public static function fromDatabase(?string $value): string
    {
        return $value ?? 'all';
    }
}
