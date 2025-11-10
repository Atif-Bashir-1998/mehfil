<?php

namespace App\Enums;

enum AdStatusType: string
{
    case PENDING = 'pending';
    case ACTIVE = 'active';
    case EXPIRED = 'expired';

    public static function all(): array
    {
        return collect(self::cases())->map(fn ($case) => [
            'value' => $case->value,
            'label' => $case->label(),
        ])->toArray();
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function options(): array
    {
        return [
            self::PENDING->value => 'pending',
            self::ACTIVE->value => 'active',
            self::EXPIRED->value => 'expired',
        ];
    }

    public function label(): string
    {
        return match($this) {
            self::PENDING => 'Pending/Draft',
            self::ACTIVE => "Active",
            self::EXPIRED => "Expired",
        };
    }
}
