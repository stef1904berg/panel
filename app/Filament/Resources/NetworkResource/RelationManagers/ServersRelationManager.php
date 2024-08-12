<?php

namespace App\Filament\Resources\NetworkResource\RelationManagers;

use App\Models\Server;
use App\Models\ServerNetwork;
use App\Services\Servers\JoinNetworkService;
use App\Services\Servers\LeaveNetworkService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class ServersRelationManager extends RelationManager
{
    protected static string $relationship = 'servers';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('name'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\AttachAction::make()
                    ->preloadRecordSelect()
                    ->after(fn (Server $server) => resolve(JoinNetworkService::class)->handle($server, $this->ownerRecord)),
            ])
            ->actions([
                Tables\Actions\DetachAction::make()
                ->after(fn (Server $server) => resolve(LeaveNetworkService::class)->handle($server, $this->ownerRecord)),
            ])
            ->bulkActions([
                Tables\Actions\DetachBulkAction::make(),
            ]);
    }

}
