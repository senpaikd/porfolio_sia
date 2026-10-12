<?php

namespace App\Filament\Resources\Projects\Schemas;

use App\Enums\ProjectStatus;
use App\Services\ProjectImageService;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ProjectForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->required()
                    ->maxLength(255),
                TextInput::make('slug')
                    ->required()
                    ->alphaDash()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true),
                TextInput::make('short_description')
                    ->required()
                    ->maxLength(500),
                Textarea::make('full_description')
                    ->required()
                    ->rows(10)
                    ->columnSpanFull(),
                FileUpload::make('main_image')
                    ->image()
                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                    ->maxSize(5120)
                    ->disk('public')
                    ->visibility('public')
                    ->saveUploadedFileUsing(fn ($file): string => app(ProjectImageService::class)->store($file, 'projects/main')),
                TextInput::make('github_url')
                    ->url()
                    ->rule('url:http,https'),
                TextInput::make('live_url')
                    ->url()
                    ->rule('url:http,https'),
                Select::make('status')
                    ->options(ProjectStatus::class)
                    ->default('in_development')
                    ->required(),
                Toggle::make('is_featured')
                    ->required(),
                Toggle::make('is_published')
                    ->required(),
                TextInput::make('display_order')
                    ->required()
                    ->numeric()
                    ->default(0),
                DatePicker::make('completion_date'),
                TextInput::make('seo_title'),
                TextInput::make('seo_description')
                    ->maxLength(160),
                Select::make('technologies')
                    ->relationship('technologies', 'name')
                    ->multiple()
                    ->preload()
                    ->searchable()
                    ->columnSpanFull(),
            ]);
    }
}
