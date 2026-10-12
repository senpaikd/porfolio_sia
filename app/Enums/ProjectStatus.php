<?php

namespace App\Enums;

enum ProjectStatus: string
{
    case InDevelopment = 'in_development';
    case Completed = 'completed';
    case Maintained = 'maintained';
    case Archived = 'archived';

    public function label(): string
    {
        return match ($this) {
            self::InDevelopment => 'In development',
            self::Completed => 'Completed',
            self::Maintained => 'Maintained',
            self::Archived => 'Archived',
        };
    }
}
