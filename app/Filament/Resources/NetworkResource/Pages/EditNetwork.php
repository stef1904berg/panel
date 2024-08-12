<?php

namespace App\Filament\Resources\NetworkResource\Pages;

use App\Enums\NetworkDriver;
use App\Filament\Resources\NetworkResource;
use App\Models\Network;
use App\Services\Network\NetworkDeletionService;
use Filament\Actions;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Pages\EditRecord;

class EditNetwork extends EditRecord
{
    protected static string $resource = NetworkResource::class;

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->prefix('pnw_')
                    ->maxLength(32)
                    ->minLength(2)
                    ->required()
                    ->helperText("Name of the docker network. Prefixed with 'pnw_' to be able to easily identify networks that belong to Pelican"),
                Select::make('driver')
                    ->options(NetworkDriver::class)
                    ->required()
                    ->label('Network driver'),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->after(fn (Network $network) => resolve(NetworkDeletionService::class)->handle($network)),
        ];
    }
}
