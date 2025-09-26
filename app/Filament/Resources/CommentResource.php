<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CommentResource\Pages;
use App\Filament\Resources\CommentResource\RelationManagers;
use App\Models\Comment;
use Filament\Forms;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CommentResource extends Resource
{
    protected static ?string $model = Comment::class;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-bottom-center-text';
    protected static ?string $navigationGroup = 'Management';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label('Author')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('post.title')
                    ->label('Post')
                    ->limit(20)
                    ->searchable()
                    ->sortable(),

                TextColumn::make('content')
                    ->label('Comment')
                    ->limit(50)
                    ->tooltip(fn($record): string => $record->content),

                TextColumn::make('parent.user.name')
                    ->label('Reply to')
                    ->default('—')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->label('Posted At')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                SelectFilter::make('post_id')
                    ->label('Post')
                    ->relationship('post', 'title')
                    ->searchable()
                    ->preload(),

                SelectFilter::make('user_id')
                    ->label('Author')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload(),

                SelectFilter::make('replies')
                    ->label('Comment Type')
                    ->options([
                        'main' => 'Main Comments',
                        'reply' => 'Replies',
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        if ($data['value'] === 'main') {
                            return $query->whereNull('parent_id');
                        } elseif ($data['value'] === 'reply') {
                            return $query->whereNotNull('parent_id');
                        }
                        return $query;
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListComments::route('/'),
            'view' => Pages\ViewComment::route('/{record}')
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }
}
