<?php

namespace App\Filament\Resources\FlagResource\Pages;

use App\Filament\Resources\FlagResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\Grid;

class ViewFlag extends ViewRecord
{
    protected static string $resource = FlagResource::class;

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Flag Details')
                    ->schema([
                        TextEntry::make('flaggable_type')
                            ->label('Content Type')
                            ->formatStateUsing(fn ($state) => match($state) {
                                'App\\Models\\Post' => 'Post',
                                'App\\Models\\Comment' => 'Comment',
                                'App\\Models\\User' => 'User',
                                'App\\Models\\Image' => 'Image',
                                default => class_basename($state)
                            }),

                        TextEntry::make('reason'),

                        TextEntry::make('flaggedBy.name')
                            ->label('Reported By'),

                        TextEntry::make('status')
                            ->badge()
                            ->color(fn ($state) => match($state) {
                                'pending' => 'warning',
                                'reviewed' => 'info',
                                'resolved' => 'success',
                                default => 'gray'
                            }),

                        TextEntry::make('created_at')
                            ->label('Reported At')
                            ->dateTime(),
                    ])
                    ->columns(2),

                Section::make('Flagged Content')
                    ->schema(function ($record) {
                        $flaggable = $record->flaggable;
                        if (!$flaggable) {
                            return [
                                TextEntry::make('')->label('Content has been deleted')
                            ];
                        }

                        return match(get_class($flaggable)) {
                            'App\\Models\\Post' => [
                                TextEntry::make('flaggable.title')
                                    ->label('Post Title'),
                                TextEntry::make('flaggable.content')
                                    ->label('Post Content')
                                    ->html()
                                    ->columnSpanFull(),
                            ],
                            'App\\Models\\Comment' => [
                                TextEntry::make('flaggable.content')
                                    ->label('Comment Content')
                                    ->html()
                                    ->columnSpanFull(),
                                TextEntry::make('flaggable.post.title')
                                    ->label('On Post'),
                            ],
                            'App\\Models\\User' => [
                                TextEntry::make('flaggable.name')
                                    ->label('User Name'),
                                TextEntry::make('flaggable.email')
                                    ->label('Email'),
                            ],
                            default => [
                                TextEntry::make('')->label('Content type: ' . class_basename($flaggable))
                            ]
                        };
                    })
                    ->columns(2),

                Section::make('Resolution Details')
                    ->schema([
                        TextEntry::make('admin_notes')
                            ->label('Admin Notes')
                            ->columnSpanFull()
                            ->placeholder('No notes added'),

                        TextEntry::make('resolved_at')
                            ->label('Resolved At')
                            ->dateTime()
                            ->placeholder('Not resolved'),

                        TextEntry::make('resolvedBy.name')
                            ->label('Resolved By')
                            ->placeholder('Not resolved'),
                    ])
                    ->columns(2)
                    ->hidden(fn ($record) => $record->status !== 'resolved'),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
