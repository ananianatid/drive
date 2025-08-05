<?php

namespace App\Filament\Resources\VehiculeResource\Pages;

use App\Filament\Resources\VehiculeResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewVehicule extends ViewRecord
{
    protected static string $resource = VehiculeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
