<?php

namespace App\Filament\Resources\Projects\RelationManagers;

use App\Services\ProjectImageService;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ImagesRelationManager extends RelationManager
{
    protected static string $relationship = 'images';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            FileUpload::make('path')->label('Screenshot')->required()->image()->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])->maxSize(5120)->disk('public')->visibility('public')->saveUploadedFileUsing(fn ($file): string => app(ProjectImageService::class)->store($file, 'projects/screenshots')),
            TextInput::make('alt_text')->maxLength(255),
            TextInput::make('display_order')->numeric()->default(0)->required(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table->columns([
            ImageColumn::make('path')->disk('public'),
            TextColumn::make('alt_text')->searchable(),
            TextColumn::make('display_order')->sortable(),
        ])->defaultSort('display_order')->reorderable('display_order')->headerActions([CreateAction::make()])->recordActions([EditAction::make(), DeleteAction::make()])->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }
}
