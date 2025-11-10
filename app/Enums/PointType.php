<?php

namespace App\Enums;

enum PointType: string
{

    case CREATED_POST = 'created-post';
    case CREATED_COMMENT = 'created-comment';
    case REACTED_POST = 'reacted-post';
    case REACTED_COMMENT = 'reacted-comment';
    case POST_GOT_REACTION = 'post-got-reaction';
    case POST_GOT_COMMENT = 'post-got-comment';
    case COMMENT_GOT_COMMENT = 'comment-got-comment';

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
            self::CREATED_POST->value => 'created-post',
            self::CREATED_COMMENT->value => 'created-comment',
            self::REACTED_POST->value => 'reacted-post',
            self::REACTED_COMMENT->value => 'reacted-comment',
            self::POST_GOT_REACTION->value => 'post-got-reaction',
            self::POST_GOT_COMMENT->value => 'post-got-comment',
            self::COMMENT_GOT_COMMENT->value => 'comment-got-comment',
        ];
    }

    public function label(): string
    {
        return match($this) {
            self::CREATED_POST => 'User publishes a post',
            self::CREATED_COMMENT => "User writes a comment",
            self::REACTED_POST => "User reacts to a post",
            self::REACTED_COMMENT => "User reacts to a comment",
            self::POST_GOT_REACTION => "Post got a reaction",
            self::POST_GOT_COMMENT => 'Post got a comment',
            self::COMMENT_GOT_COMMENT => 'Comment got a reply',
        };
    }
}
