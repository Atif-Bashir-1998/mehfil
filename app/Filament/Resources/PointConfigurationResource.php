<?php

namespace App\Filament\Resources;

use App\Enums\PointType;
use App\Filament\Resources\PointConfigurationResource\Pages;
use App\Filament\Resources\PointConfigurationResource\RelationManagers;
use App\Models\PointConfiguration;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PointConfigurationResource extends Resource
{
    protected static ?string $model = PointConfiguration::class;

    protected static ?string $navigationIcon = 'heroicon-o-sparkles';
    protected static ?string $navigationGroup = 'Management';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Configuration Details')
                    ->schema([
                        Forms\Components\Select::make('action_type')
                            ->options(PointType::options())
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->disabled(fn ($record) => $record !== null) // Disable editing for existing records
                            ->helperText('The type of action that earns/spends points'),

                        Forms\Components\Textarea::make('description')
                            ->required()
                            ->rows(2)
                            ->maxLength(500)
                            ->helperText('Description of what this point configuration does'),

                        Forms\Components\TextInput::make('points')
                            ->required()
                            ->numeric()
                            ->step(1)
                            ->helperText('Positive numbers award points, negative numbers spend points')
                            ->suffix('points'),

                        Forms\Components\Toggle::make('is_active')
                            ->required()
                            ->default(true)
                            ->helperText('Enable or disable this point configuration'),
                    ])
                    ->columns(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('action_type')
                    ->label('Action Type')
                    ->formatStateUsing(fn ($state) => PointType::tryFrom($state)?->name ?? $state)
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('description')
                    ->limit(50)
                    ->searchable()
                    ->tooltip(fn ($record) => $record->description),

                Tables\Columns\TextColumn::make('points')
                    ->sortable()
                    ->color(fn ($record) => $record->points >= 0 ? 'success' : 'danger')
                    ->formatStateUsing(fn ($state) => $state >= 0 ? "+{$state}" : $state)
                    ->suffix(' points'),

                Tables\Columns\IconColumn::make('is_active')
                    ->boolean()
                    ->label('Active')
                    ->sortable(),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Last Updated')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('action_type')
                    ->options(PointType::options())
                    ->label('Action Type'),

                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Active Status'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('toggleActive')
                    ->label(fn ($record) => $record->is_active ? 'Deactivate' : 'Activate')
                    ->icon(fn ($record) => $record->is_active ? 'heroicon-o-eye-slash' : 'heroicon-o-eye')
                    ->color(fn ($record) => $record->is_active ? 'warning' : 'success')
                    ->action(function ($record) {
                        $record->update(['is_active' => !$record->is_active]);
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('activate')
                        ->label('Activate Selected')
                        ->icon('heroicon-o-eye')
                        ->action(fn ($records) => $records->each->update(['is_active' => true])),
                    Tables\Actions\BulkAction::make('deactivate')
                        ->label('Deactivate Selected')
                        ->icon('heroicon-o-eye-slash')
                        ->action(fn ($records) => $records->each->update(['is_active' => false])),
                ]),
            ])
            ->defaultSort('action_type')
            ->reorderable('action_type');
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
            'index' => Pages\ListPointConfigurations::route('/'),
            'create' => Pages\CreatePointConfiguration::route('/create'),
            'edit' => Pages\EditPointConfiguration::route('/{record}/edit'),
        ];
    }
}
