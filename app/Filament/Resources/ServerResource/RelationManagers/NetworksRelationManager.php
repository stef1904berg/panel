<?php

namespace App\Filament\Resources\ServerResource\RelationManagers;

use App\Exceptions\Http\DockerNetworkException;
use App\Models\Network;
use App\Services\Servers\JoinNetworkService;
use App\Services\Servers\LeaveNetworkService;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Actions\AttachAction;
use Filament\Tables\Actions\DetachAction;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

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
                AttachAction::make()
                    ->preloadRecordSelect()
                    ->attachAnother(false)
                    ->recordSelectOptionsQuery(fn(Builder $query) => $query->where('node_id', $this->ownerRecord->node_id)->orWhere('driver', NetworkDriver::Overlay))
                    ->label("Join network")
                    ->before(function (AttachAction $action) {
                        try {
                            resolve(JoinNetworkService::class)->handle($this->ownerRecord, $action->getFormData()['recordId']);
                        } catch (DockerNetworkException $exception) {
                            Notification::make()->title($exception->getMessage())->danger()->send();
                            $action->cancel(true);
                        }
                    }),
            ])
            ->actions([
                Tables\Actions\DetachAction::make()
                    ->label("Leave")
                    ->before(function (DetachAction $action) {
                        try {
                            resolve(LeaveNetworkService::class)->handle($this->ownerRecord, $action->getRecord());
                        } catch (DockerNetworkException $exception) {
                            Notification::make()->title($exception->getMessage())->danger()->send();
                            $action->cancel(true);
                        }
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DetachBulkAction::make()
                        ->label("Leave networks")
                        ->after(function (Collection $networks) {
                            $networks->each(
                                fn(Network $network) => resolve(LeaveNetworkService::class)->handle($this->ownerRecord, $network)
                            );
                        }),
                ]),
            ]);
    }
}
