<?php

namespace App\Filament\Resources\Technologies\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class TechnologyForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()->maxLength(255)->unique(ignoreRecord: true),
                TextInput::make('slug')
                    ->required()->alphaDash()->maxLength(255)->unique(ignoreRecord: true),
                TextInput::make('category')->maxLength(255),
                Toggle::make('is_published')
                    ->required(),
                TextInput::make('display_order')
                    ->required()
                    ->numeric()
                    ->default(0),
            ]);
    }
}
