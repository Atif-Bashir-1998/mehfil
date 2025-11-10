<?php

namespace App\Enums;

enum PointTransactionType: string
{
    case EARNED = 'earned';
    case SPENT = 'spent';
    case BONUS = 'bonus';
    case PENALTY = 'penalty';

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
            self::EARNED->value => 'earned',
            self::SPENT->value => 'spent',
            self::BONUS->value => 'bonus',
            self::PENALTY->value => 'penalty',
        ];
    }

    public function label(): string
    {
        return match($this) {
            self::EARNED => 'Earned',
            self::SPENT => "Spent",
            self::BONUS => "Bonus",
            self::PENALTY => "Penalty",
        };
    }
}
