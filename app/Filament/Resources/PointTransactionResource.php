<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PointTransactionResource\Pages;
use App\Filament\Resources\PointTransactionResource\RelationManagers;
use App\Models\PointTransaction;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;

class PointTransactionResource extends Resource
{
    protected static ?string $model = PointTransaction::class;

    protected static ?string $navigationIcon = 'heroicon-o-cursor-arrow-rays';
    protected static ?string $navigationGroup = 'Reward System';
    protected static ?string $modelLabel = 'Transactions';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('points')
                    ->sortable()
                    ->color(fn ($record) => $record->points >= 0 ? 'success' : 'danger')
                    ->formatStateUsing(fn ($state) => $state >= 0 ? "+{$state}" : $state),

                Tables\Columns\TextColumn::make('type')
                    ->badge()
                    ->color(fn ($state) => match($state) {
                        'earned' => 'success',
                        'spent' => 'danger',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('action_type')
                    ->label('Action')
                    ->formatStateUsing(fn ($state) => Str::title(str_replace('_', ' ', $state))),

                Tables\Columns\TextColumn::make('description')
                    ->limit(50)
                    ->tooltip(fn ($record) => $record->description),

                Tables\Columns\TextColumn::make('transactionable_type')
                    ->label('Related To')
                    ->formatStateUsing(fn ($state) => $state ? class_basename($state) : '—'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Date')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->options([
                        'earned' => 'Earned',
                        'spent' => 'Spent',
                    ]),

                Tables\Filters\SelectFilter::make('user')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([]);
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
            'index' => Pages\ListPointTransactions::route('/'),
            'create' => Pages\CreatePointTransaction::route('/create'),
            'edit' => Pages\EditPointTransaction::route('/{record}/edit'),
        ];
    }
}
