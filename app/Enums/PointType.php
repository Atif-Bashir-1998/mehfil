<?php

namespace App\Enums;

enum PointType: string
{
    case POST_CREATED = 'post-created';
    case POST_REACTED = 'post-reacted';
    case POST_COMMENTED = 'post-commented';
    case COMMENT_REACTED = 'comment-reacted';
    case COMMENT_CREATED = 'comment-created';
    case COMMENT_REPLIED = 'comment-replied';
    case REACTED = 'reacted';

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
            self::POST_CREATED->value => 'post-created',
            self::POST_REACTED->value => 'post-reacted',
            self::POST_COMMENTED->value => 'post-commented',
            self::COMMENT_REACTED->value => 'comment-reacted',
            self::COMMENT_CREATED->value => 'comment-created',
            self::COMMENT_REPLIED->value => 'comment-replied',
            self::REACTED->value => 'reacted',
        ];
    }

    public function label(): string
    {
        return match($this) {
            self::POST_CREATED => 'User publishes a post',
            self::POST_REACTED => "User's post got a reaction",
            self::POST_COMMENTED => "User's post got a comment",
            self::COMMENT_REACTED => "User's comment got a reaction",
            self::COMMENT_CREATED => "User writes a comment",
            self::COMMENT_REPLIED => "User's comment got a reply",
            self::REACTED => "User reacting to a post",
        };
    }
}
