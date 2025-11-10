<?php

namespace App\Filament\Resources\PointConfigurationResource\Pages;

use App\Filament\Resources\PointConfigurationResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPointConfiguration extends EditRecord
{
    protected static string $resource = PointConfigurationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
