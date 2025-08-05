<?php

namespace App\Filament\Resources\AcademicClassResource\Pages;

use App\Filament\Resources\AcademicClassResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAcademicClass extends EditRecord
{
    protected static string $resource = AcademicClassResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
