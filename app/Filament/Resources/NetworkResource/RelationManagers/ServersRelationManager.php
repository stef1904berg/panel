<?php

namespace App\Filament\Resources\NetworkResource\RelationManagers;

use App\Exceptions\Http\DockerNetworkException;
use App\Services\Servers\JoinNetworkService;
use App\Services\Servers\LeaveNetworkService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Actions\AttachAction;
use Filament\Tables\Actions\DetachAction;
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
                    ->label("Join server")
                    ->attachAnother(false)
                    ->successNotificationTitle("Server joined network")
                    ->before(function (AttachAction $action) {
                        try {
                            resolve(JoinNetworkService::class)->handle($action->getFormData()['recordId'], $this->ownerRecord);
                        } catch (DockerNetworkException $exception) {
                            Notification::make()->title($exception->getMessage())->danger()->send();
                            $action->cancel(true);
                        }
                    }),])
            ->actions([
                Tables\Actions\DetachAction::make()
                    ->label("Leave")
                    ->successNotificationTitle("Server left network")
                    ->before(function (DetachAction $action) {
                        try {
                            resolve(LeaveNetworkService::class)->handle($action->getRecord(), $this->ownerRecord,);
                        } catch (DockerNetworkException $exception) {
                            Notification::make()->title($exception->getMessage())->danger()->send();
                            $action->cancel(true);
                        }
                    }),
            ]);
    }

}
