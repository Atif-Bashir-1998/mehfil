<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FlagResource\Pages;
use App\Filament\Resources\FlagResource\RelationManagers;
use App\Models\Flag;
use Filament\Forms;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;

class FlagResource extends Resource
{
    protected static ?string $model = Flag::class;

    protected static ?string $navigationIcon = 'heroicon-o-flag';
    protected static ?string $navigationGroup = 'Moderation';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'reviewed' => 'Reviewed',
                        'resolved' => 'Resolved',
                    ])
                    ->required(),

                Textarea::make('admin_notes')
                    ->label('Admin Notes')
                    ->rows(3),

                DateTimePicker::make('resolved_at')
                    ->label('Resolved At'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('flaggable_type')
                    ->label('Type')
                    ->formatStateUsing(fn ($state) => match($state) {
                        'App\Models\Post' => 'Post',
                        'App\Models\Comment' => 'Comment',
                        'App\Models\User' => 'User',
                        default => $state
                    }),

                TextColumn::make('flaggable_id')
                    ->label('Item')
                    ->formatStateUsing(function ($record) {
                        $flaggable = $record->flaggable;
                        if (!$flaggable) return 'Deleted Item';

                        return match(get_class($flaggable)) {
                            'App\Models\Post' => 'Post: ' . Str::limit($flaggable->title, 30),
                            'App\Models\Comment' => 'Comment: ' . Str::limit($flaggable->content, 30),
                            'App\Models\User' => 'User: ' . $flaggable->name,
                            default => 'Unknown'
                        };
                    }),

                TextColumn::make('reason')
                    ->limit(50),

                TextColumn::make('flaggedBy.name')
                    ->label('Reported By'),

                SelectColumn::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'reviewed' => 'Reviewed',
                        'resolved' => 'Resolved',
                    ]),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'reviewed' => 'Reviewed',
                        'resolved' => 'Resolved',
                    ]),

                SelectFilter::make('flaggable_type')
                    ->label('Type')
                    ->options([
                        'App\Models\Post' => 'Posts',
                        'App\Models\Comment' => 'Comments',
                        'App\Models\User' => 'Users',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListFlags::route('/'),
            'create' => Pages\CreateFlag::route('/create'),
            'edit' => Pages\EditFlag::route('/{record}/edit'),
        ];
    }
}
