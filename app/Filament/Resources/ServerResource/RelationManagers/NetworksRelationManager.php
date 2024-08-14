<?php

namespace App\Filament\Resources\ServerResource\RelationManagers;

use App\Models\Network;
use App\Services\Servers\JoinNetworkService;
use App\Services\Servers\LeaveNetworkService;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class NetworksRelationManager extends RelationManager
{
    protected static string $relationship = 'networks';

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('name'),
                Tables\Columns\TextColumn::make('driver')->badge(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\AttachAction::make()
                    ->preloadRecordSelect()
                    ->after(fn (Network $network) => resolve(JoinNetworkService::class)->handle($this->ownerRecord, $network)),
            ])
            ->actions([
                Tables\Actions\DetachAction::make()
                    ->after(fn (Network $network) => resolve(LeaveNetworkService::class)->handle($this->ownerRecord, $network)),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DetachBulkAction::make(),
                ]),
            ]);
    }
}
