<?php

namespace App\Filament\Resources\IdentityCardResource\Pages;

use App\Filament\Resources\IdentityCardResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewIdentityCard extends ViewRecord
{
    protected static string $resource = IdentityCardResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
