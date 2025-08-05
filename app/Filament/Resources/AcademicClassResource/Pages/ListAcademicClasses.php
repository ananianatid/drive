<?php

namespace App\Filament\Resources\AcademicClassResource\Pages;

use App\Filament\Resources\AcademicClassResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAcademicClasses extends ListRecords
{
    protected static string $resource = AcademicClassResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
