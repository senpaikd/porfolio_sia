<?php

namespace App\Filament\Resources\Education\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class EducationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('institution')
                    ->required()->maxLength(255),
                TextInput::make('qualification')
                    ->required()->maxLength(255),
                TextInput::make('field')->maxLength(255),
                DatePicker::make('start_date'),
                DatePicker::make('end_date'),
                Textarea::make('description')
                    ->rows(6)
                    ->columnSpanFull(),
                Toggle::make('is_published')
                    ->required(),
                TextInput::make('display_order')
                    ->required()
                    ->numeric()
                    ->default(0),
            ]);
    }
}
