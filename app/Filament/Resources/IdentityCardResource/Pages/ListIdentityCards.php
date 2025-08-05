<?php

namespace App\Filament\Resources\IdentityCardResource\Pages;

use App\Filament\Resources\IdentityCardResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListIdentityCards extends ListRecords
{
    protected static string $resource = IdentityCardResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
