<?php

namespace App\Filament\Resources\NetworkResource\Pages;

use App\Enums\NetworkDriver;
use App\Filament\Resources\NetworkResource;
use App\Models\Network;
use App\Services\Network\NetworkDeletionService;
use Filament\Actions;
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
                    ->maxLength(32)
                    ->minLength(2)
                    ->disabled(),
                Select::make('driver')
                    ->options(NetworkDriver::class)
                    ->prefixIcon('tabler-router')
                    ->disabled()
                    ->label('Network driver'),
                Select::make('node_id')
                    ->label('Node')
                    ->prefixIcon('tabler-server-2')
                    ->relationship('node', 'name')
                    ->preload()
                    ->disabled()
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
