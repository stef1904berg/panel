<?php

namespace App\Filament\Resources\NetworkResource\Pages;

use App\Enums\NetworkDriver;
use App\Filament\Resources\NetworkResource;
use App\Models\Node;
use App\Services\Network\NetworkCreationService;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateNetwork extends CreateRecord
{
    protected static string $resource = NetworkResource::class;

    protected static bool $canCreateAnother = false;

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->prefix('pnw_')
                    ->maxLength(32)
                    ->minLength(2)
                    ->required()
                    ->hintIcon('tabler-question-mark')
                    ->hintIconTooltip("Network names are prefixed with 'pnw_' to easily identify network names."),
                Select::make('driver')
                    ->options(NetworkDriver::class)
                    ->prefixIcon('tabler-router')
                    ->required()
                    ->searchable()
                    ->label('Network driver')
                    ->helperText('When selecting a Overlay driver, you must select the worker node.'),
                Select::make('node_id')
                    ->label('Node')
                    ->prefixIcon('tabler-server-2')
                    ->default(fn () => (Node::query()->latest()->first())?->id)
                    ->relationship('node', 'name')
                    ->preload()
                    ->required()
                    ->searchable(),
            ]);
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['name'] = 'pnw_' . $data['name'];

        return $data;
    }

    protected function handleRecordCreation(array $data): Model
    {
        /** @var NetworkCreationService $service */
        $service = resolve(NetworkCreationService::class);

        return $service->handle($data);
    }
}
