<?php

namespace App\Filament\Resources\AcademicClassResource\Pages;

use App\Filament\Resources\AcademicClassResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewAcademicClass extends ViewRecord
{
    protected static string $resource = AcademicClassResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
