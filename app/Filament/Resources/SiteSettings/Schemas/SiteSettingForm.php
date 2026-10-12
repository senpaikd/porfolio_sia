<?php

namespace App\Filament\Resources\SiteSettings\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class SiteSettingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('owner_name')
                    ->required()->maxLength(255),
                TextInput::make('professional_title')->maxLength(255),
                Textarea::make('biography')
                    ->rows(8)
                    ->columnSpanFull(),
                TextInput::make('email')
                    ->label('Email address')
                    ->email(),
                TextInput::make('github_url')
                    ->url()->rule('url:http,https'),
                TextInput::make('linkedin_url')
                    ->url()->rule('url:http,https'),
                TextInput::make('resume_path'),
                TextInput::make('availability'),
                TextInput::make('seo_title')->maxLength(70),
                TextInput::make('seo_description')->maxLength(160),
            ]);
    }
}
