<?php

namespace App\Filament\Resources;

use App\Enums\AdStatusType;
use App\Filament\Resources\AdResource\Pages;
use App\Filament\Resources\AdResource\RelationManagers;
use App\Models\Ad;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AdResource extends Resource
{
    protected static ?string $model = Ad::class;

    protected static ?string $navigationIcon = 'heroicon-o-megaphone';
    protected static ?string $navigationGroup = 'Advertising';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Ad Details')
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),

                        Forms\Components\TextInput::make('title')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\Textarea::make('content')
                            ->required()
                            ->rows(3),

                        Forms\Components\TextInput::make('image_url')
                            ->label('Image URL')
                            ->url()
                            ->maxLength(500),

                        Forms\Components\TextInput::make('target_url')
                            ->required()
                            ->url()
                            ->maxLength(500),
                    ])->columns(1),

                Forms\Components\Section::make('Campaign Settings')
                    ->schema([
                        Forms\Components\TextInput::make('points_spent')
                            ->required()
                            ->numeric()
                            ->minValue(1),

                        Forms\Components\Select::make('status')
                            ->options(AdStatusType::options())
                            ->required(),

                        Forms\Components\DateTimePicker::make('starts_at')
                            ->required(),

                        Forms\Components\DateTimePicker::make('ends_at')
                            ->required()
                            ->after('starts_at'),
                    ])->columns(2),
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

                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->limit(30),

                Tables\Columns\TextColumn::make('points_spent')
                    ->sortable()
                    ->formatStateUsing(fn ($state) => number_format($state)),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn ($state) => match($state) {
                        AdStatusType::ACTIVE => 'success',
                        AdStatusType::EXPIRED => 'gray',
                        AdStatusType::PENDING => 'info',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('clicks')
                    ->sortable()
                    ->formatStateUsing(fn ($state) => number_format($state)),

                Tables\Columns\TextColumn::make('impressions')
                    ->sortable()
                    ->formatStateUsing(fn ($state) => number_format($state)),

                Tables\Columns\TextColumn::make('ctr')
                    ->label('CTR')
                    ->getStateUsing(fn ($record) => $record->impressions > 0
                        ? round(($record->clicks / $record->impressions) * 100, 2) . '%'
                        : '0%'),

                Tables\Columns\TextColumn::make('starts_at')
                    ->dateTime()
                    ->sortable(),

                Tables\Columns\TextColumn::make('ends_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(AdStatusType::options()),

                Tables\Filters\SelectFilter::make('user')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload(),

                Tables\Filters\Filter::make('active')
                    ->label('Currently Active')
                    ->query(fn ($query) => $query->where('status', AdStatusType::ACTIVE)
                        ->where('starts_at', '<=', now())
                        ->where('ends_at', '>=', now())),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('view_ad')
                    ->label('View')
                    ->url(fn ($record) => $record->target_url)
                    ->openUrlInNewTab()
                    ->icon('heroicon-o-eye'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('activate')
                        ->label('Activate Selected')
                        ->action(fn ($records) => $records->each->update(['status' => AdStatusType::ACTIVE])),
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
            'index' => Pages\ListAds::route('/'),
            'create' => Pages\CreateAd::route('/create'),
            'edit' => Pages\EditAd::route('/{record}/edit'),
        ];
    }
}
