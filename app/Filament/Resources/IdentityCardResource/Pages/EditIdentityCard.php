<?php

namespace App\Filament\Resources\IdentityCardResource\Pages;

use App\Filament\Resources\IdentityCardResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditIdentityCard extends EditRecord
{
    protected static string $resource = IdentityCardResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
