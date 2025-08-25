<?php

namespace App\Enums;

enum ReactionType: string
{
    case LIKE = 'like';
    case LOVE = 'love';
    case LAUGH = 'laugh';
    case SAD = 'sad';
    case ANGRY = 'angry';

    public static function all(): array
    {
        return collect(self::cases())->map(fn ($case) => [
            'value' => $case->value,
            'label' => $case->label(),
            'icon' => $case->icon(),
            'color' => $case->color()
        ])->toArray();
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function options(): array
    {
        return [
            self::LIKE->value => 'Like',
            self::LOVE->value => 'Love',
            self::LAUGH->value => 'Laugh',
            self::SAD->value => 'Sad',
            self::ANGRY->value => 'Angry',
        ];
    }

    public function label(): string
    {
        return match($this) {
            self::LIKE => 'Like',
            self::LOVE => 'Love',
            self::LAUGH => 'Laugh',
            self::SAD => 'Sad',
            self::ANGRY => 'Angry',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::LIKE => 'blue',
            self::LOVE => 'red',
            self::LAUGH => 'yellow',
            self::SAD => 'yellow-lighten-1',
            self::ANGRY => 'orange-accent-4',
        };
    }

    public function icon(): string
    {
        return match($this) {
            self::LIKE => 'mdi-thumb-up',
            self::LOVE => 'mdi-heart',
            self::LAUGH => 'mdi-emoticon-happy-outline',
            self::SAD => 'mdi-emoticon-cry-outline',
            self::ANGRY => 'mdi-emoticon-angry-outline',
        };
    }
}
