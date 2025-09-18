<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationGroup = 'Management';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),

                TextInput::make('email')
                    ->email()
                    ->required()

                    ->unique(ignoreRecord: true),

                TextInput::make('password')
                    ->password()
                    ->required(fn ($context) => $context === 'create')
                    ->dehydrateStateUsing(fn ($state) => !empty($state) ? Hash::make($state) : null)
                    ->revealable()
                    ->visibleOn('create')
                    ->maxLength(255),

                DateTimePicker::make('last_login_at')
                    ->label('Last Login')
                    ->visibleOn('edit')
                    ->disabled()
                    ->readOnly(),

                DateTimePicker::make('created_at')
                    ->label('Created At')
                    ->default(fn ($record) => $record?->created_at)
                    ->disabled()
                    ->visibleOn('edit'),

                Section::make('Email verification')
                    ->description('If selected, user email will be automatically verified.')
                    ->schema([
                        Toggle::make('email_verified_at')
                            ->label('Email Verified')
                            ->onIcon('heroicon-o-check-circle')
                            ->offIcon('heroicon-o-x-circle')
                            ->dehydrateStateUsing(function ($state) {
                                return $state ? now() : null;
                            })
                            ->afterStateHydrated(function (Toggle $component, $state) {
                                // Set the toggle state based on whether email_verified_at is not null
                                $component->state(!is_null($state));
                            }),
                    ]),


                Section::make('Roles for user')
                    ->description('Check all the roles you want to assign this user')
                    ->schema([
                        CheckboxList::make('roles')
                            ->relationship('roles', 'name')
                            ->options(Role::pluck('name', 'id')->toArray())
                            ->label('Assign Roles')
                            ->columns(2)
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('profile_image.path')
                    ->label('Profile Image')
                    ->circular(),

                TextColumn::make('name')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('email')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('slogan')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('occupation')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('location')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('roles.name')
                    ->label('Roles')
                    ->badge()
                    ->color('info')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('email_verified_at')
                    ->label('Verified At')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->label('Created At')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
