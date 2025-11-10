<?php

namespace App\Filament\Resources\PointConfigurationResource\Pages;

use App\Filament\Resources\PointConfigurationResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPointConfigurations extends ListRecords
{
    protected static string $resource = PointConfigurationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
