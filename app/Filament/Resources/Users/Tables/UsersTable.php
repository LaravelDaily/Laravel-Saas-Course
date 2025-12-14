<?php

namespace App\Filament\Resources\Users\Tables;

use App\Enums\RoleEnum;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->whereDoesntHave('roles', fn (Builder $q) => $q->where('name', RoleEnum::SuperAdmin->value)))
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('email')
                    ->label('Email Address')
                    ->searchable()
                    ->sortable()
                    ->copyable(),
                TextColumn::make('roles.name')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'superadmin' => RoleEnum::SuperAdmin->label(),
                        'admin' => RoleEnum::Admin->label(),
                        'user' => RoleEnum::User->label(),
                        'viewer' => RoleEnum::Viewer->label(),
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'superadmin' => 'primary',
                        'admin' => 'danger',
                        'user' => 'success',
                        'viewer' => 'warning',
                        default => 'gray',
                    }),
                TextColumn::make('organization.name')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                IconColumn::make('email_verified_at')
                    ->label('Verified')
                    ->boolean()
                    ->sortable(),
                TextColumn::make('subscriptions_count')
                    ->counts('subscriptions')
                    ->label('Subscriptions')
                    ->badge()
                    ->color('info'),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('role')
                    ->relationship('roles', 'name')
                    ->options([
                        RoleEnum::Admin->value => RoleEnum::Admin->label(),
                        RoleEnum::User->value => RoleEnum::User->label(),
                        RoleEnum::Viewer->value => RoleEnum::Viewer->label(),
                    ])
                    ->preload()
                    ->native(false),
                Filter::make('has_subscription')
                    ->label('Has Active Subscription')
                    ->query(fn (Builder $query): Builder => $query->whereHas('subscriptions', fn (Builder $q) => $q->where('stripe_status', 'active')))
                    ->toggle(),
                Filter::make('verified')
                    ->label('Email Verified')
                    ->query(fn (Builder $query): Builder => $query->whereNotNull('email_verified_at'))
                    ->toggle(),
            ])
            ->recordActions([
                EditAction::make(),
                Action::make('send_notification')
                    ->label('Send Notification')
                    ->icon('heroicon-o-bell')
                    ->color('info')
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        Notification::make()
                            ->title('Notification Sent')
                            ->body("Notification sent to {$record->name}")
                            ->success()
                            ->send();
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
