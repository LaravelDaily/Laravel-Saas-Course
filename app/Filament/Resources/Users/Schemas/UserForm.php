<?php

namespace App\Filament\Resources\Users\Schemas;

use App\Enums\RoleEnum;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('User Information')
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('email')
                            ->label('Email Address')
                            ->email()
                            ->required()
                            ->maxLength(255),
                        TextInput::make('password')
                            ->password()
                            ->dehydrated(fn ($state) => filled($state))
                            ->required(fn (string $context): bool => $context === 'create')
                            ->minLength(8)
                            ->maxLength(255),
                    ])
                    ->columns(2),

                Section::make('Role & Organization')
                    ->schema([
                        Select::make('role')
                            ->options([
                                RoleEnum::Admin->value => RoleEnum::Admin->label(),
                                RoleEnum::User->value => RoleEnum::User->label(),
                                RoleEnum::Viewer->value => RoleEnum::Viewer->label(),
                            ])
                            ->default(RoleEnum::User->value)
                            ->required()
                            ->native(false),
                        Select::make('organization_id')
                            ->relationship('organization', 'name')
                            ->searchable()
                            ->preload()
                            ->native(false),
                    ])
                    ->columns(2),

                Section::make('Settings')
                    ->schema([
                        Toggle::make('email_notifications')
                            ->label('Email Notifications')
                            ->default(true),
                        DateTimePicker::make('email_verified_at')
                            ->label('Email Verified At')
                            ->native(false),
                    ])
                    ->columns(2),
            ]);
    }
}
