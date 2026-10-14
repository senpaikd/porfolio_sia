<?php

namespace App\Filament\Resources\Certifications\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class CertificationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('title')->required()->maxLength(255),
            TextInput::make('issuer')->required()->maxLength(255),
            DatePicker::make('completion_date'),
            FileUpload::make('certificate_path')
                ->label('Certificate PDF')
                ->required()
                ->acceptedFileTypes(['application/pdf'])
                ->maxSize(5120)
                ->disk('public')
                ->directory('certificates')
                ->visibility('public')
                ->getUploadedFileNameForStorageUsing(
                    fn (TemporaryUploadedFile $file): string => Str::uuid().'.'.mb_strtolower($file->getClientOriginalExtension()),
                )
                ->downloadable()
                ->openable(),
            Toggle::make('is_featured')->required(),
            Toggle::make('is_published')->required()->default(true),
            TextInput::make('display_order')->required()->numeric()->default(0),
        ]);
    }
}
