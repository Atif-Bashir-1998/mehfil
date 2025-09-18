<?php

namespace App\Filament\Resources\CommentResource\Pages;

use App\Filament\Resources\CommentResource;
use Filament\Actions;
use Filament\Forms\Components\Section;
use Filament\Infolists\Components\Section as InfoSection;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;

class ViewComment extends ViewRecord
{
    protected static string $resource = CommentResource::class;

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                InfoSection::make('Comment Details')
                    ->schema([
                        TextEntry::make('user.name')
                            ->label('Author'),

                        TextEntry::make('post.title')
                            ->label('Post'),

                        TextEntry::make('content')
                            ->label('Comment')
                            ->columnSpanFull()
                            ->html(),

                        TextEntry::make('parent.user.name')
                            ->label('Reply To')
                            ->default('—'),

                        TextEntry::make('created_at')
                            ->label('Posted At')
                            ->dateTime(),
                    ])
                    ->columns(2),

                InfoSection::make('Comment Image')
                    ->schema([
                        ImageEntry::make('image.path')
                            ->label('')
                            ->disk('public')
                            ->height(300)
                            ->visible(fn ($record) => $record->image !== null),
                    ])
                    ->hidden(fn ($record) => $record->image === null),

                InfoSection::make('Replies')
                    ->schema([
                        RepeatableEntry::make('replies')
                            ->schema([
                                TextEntry::make('user.name')
                                    ->label('Author'),

                                TextEntry::make('content')
                                    ->label('Reply')
                                    ->html(),

                                TextEntry::make('created_at')
                                    ->label('Date')
                                    ->dateTime(),
                            ])
                            ->columns(3),
                    ])
                    ->hidden(fn ($record) => $record->replies->isEmpty()),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
