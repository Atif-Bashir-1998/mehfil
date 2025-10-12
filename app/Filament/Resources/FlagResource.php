<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FlagResource\Pages;
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
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class FlagResource extends Resource
{
    protected static ?string $model = Flag::class;

    protected static ?string $navigationIcon = 'heroicon-o-flag';
    protected static ?string $navigationGroup = 'Moderation';

    protected static ?string $recordTitleAttribute = 'reason';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Flag Information')
                    ->schema([
                        Forms\Components\TextInput::make('flaggable_type')
                            ->label('Content Type')
                            ->formatStateUsing(fn ($state) => match($state) {
                                'App\\Models\\Post' => 'Post',
                                'App\\Models\\Comment' => 'Comment',
                                'App\\Models\\User' => 'User',
                                'App\\Models\\Image' => 'Image',
                                default => class_basename($state)
                            })
                            ->disabled(),

                        Forms\Components\TextInput::make('flaggable_id')
                            ->label('Content ID')
                            ->disabled(),

                        Forms\Components\TextInput::make('flagged_by')
                            ->label('Reported By')
                            ->formatStateUsing(function ($record) {
                                return $record->flaggedBy->name ?? 'Unknown User';
                            })
                            ->disabled(),
                        Forms\Components\TextInput::make('reason')
                            ->disabled(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Moderation')
                    ->schema([
                        Select::make('status')
                            ->options([
                                'pending' => 'Pending',
                                'reviewed' => 'Reviewed',
                                'resolved' => 'Resolved',
                            ])
                            ->required()
                            ->reactive(),

                        DateTimePicker::make('resolved_at')
                            ->label('Resolved At')
                            ->default(now()),

                        Textarea::make('admin_notes')
                            ->label('Admin Notes')
                            ->placeholder('Add notes about the resolution...')
                            ->columnSpan(2)
                            ->rows(3),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('flaggable_type')
                    ->label('Type')
                    ->formatStateUsing(fn ($state) => match($state) {
                        'App\\Models\\Post' => 'Post',
                        'App\\Models\\Comment' => 'Comment',
                        'App\\Models\\User' => 'User',
                        'App\\Models\\Image' => 'Image',
                        default => class_basename($state)
                    })
                    ->badge()
                    ->color(fn ($state) => match($state) {
                        'App\\Models\\Post' => 'primary',
                        'App\\Models\\Comment' => 'info',
                        'App\\Models\\User' => 'warning',
                        'App\\Models\\Image' => 'success',
                        default => 'gray'
                    }),

                TextColumn::make('flaggable_id')
                    ->label('Content')
                    ->formatStateUsing(function ($record) {
                        $flaggable = $record->flaggable;
                        if (!$flaggable) {
                            return 'Deleted Item';
                        }

                        return match(get_class($flaggable)) {
                            'App\\Models\\Post' => 'Post: ' . Str::limit($flaggable->title, 30),
                            'App\\Models\\Comment' => 'Comment: ' . Str::limit($flaggable->content, 30),
                            'App\\Models\\User' => 'User: ' . $flaggable->name,
                            'App\\Models\\Image' => 'Image: ' . ($flaggable->path ?? 'Image'),
                            default => 'Unknown: ' . class_basename($flaggable)
                        };
                    })
                    ->searchable(),

                TextColumn::make('reason')
                    ->limit(30)
                    ->tooltip(function (TextColumn $column): ?string {
                        $state = $column->getState();
                        if (strlen($state) <= 30) {
                            return null;
                        }
                        return $state;
                    }),

                TextColumn::make('flaggedBy.name')
                    ->label('Reported By')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'reviewed' => 'info',
                        'resolved' => 'success',
                        default => 'gray',
                    })
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Reported At')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),

                TextColumn::make('resolved_at')
                    ->label('Resolved At')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'reviewed' => 'Reviewed',
                        'resolved' => 'Resolved',
                    ])
                    ->default('pending'),

                SelectFilter::make('flaggable_type')
                    ->label('Content Type')
                    ->options([
                        'App\\Models\\Post' => 'Posts',
                        'App\\Models\\Comment' => 'Comments',
                        'App\\Models\\User' => 'Users',
                        'App\\Models\\Image' => 'Images',
                    ]),

                Tables\Filters\Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('created_from')
                            ->label('From Date'),
                        Forms\Components\DatePicker::make('created_until')
                            ->label('To Date'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('view_content')
                    ->label('View Content')
                    ->icon('heroicon-o-eye')
                    ->url(function (Flag $record) {
                        $flaggable = $record->flaggable;
                        if (!$flaggable) return null;

                        return match(get_class($flaggable)) {
                            'App\\Models\\Post' => route('filament.admin.resources.posts.view', $flaggable->id),
                            'App\\Models\\Comment' => route('filament.admin.resources.comments.view', $flaggable->id),
                            'App\\Models\\User' => route('filament.admin.resources.users.edit', $flaggable->id),
                            default => null
                        };
                    })
                    ->openUrlInNewTab()
                    ->hidden(fn (Flag $record) => !$record->flaggable),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('markAsResolved')
                        ->label('Mark as Resolved')
                        ->icon('heroicon-o-check')
                        ->action(function ($records) {
                            $records->each(function ($record) {
                                $record->update([
                                    'status' => 'resolved',
                                    'resolved_at' => now(),
                                    'resolved_by' => Auth::id(),
                                ]);
                            });
                        })
                        ->requiresConfirmation(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
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
            'view' => Pages\ViewFlag::route('/{record}'),
            'edit' => Pages\EditFlag::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['flaggable', 'flaggedBy', 'resolvedBy'])
            ->latest();
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['reason', 'flaggedBy.name'];
    }
}
