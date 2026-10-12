<?php

namespace App\Models;

use App\Casts\NormalizedHttpUrl;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SiteSetting extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected function casts(): array
    {
        return ['github_url' => NormalizedHttpUrl::class, 'linkedin_url' => NormalizedHttpUrl::class];
    }

    public static function current(): self
    {
        return static::query()->first() ?? static::query()->create([
            'owner_name' => 'Placeholder: Your Name',
            'professional_title' => 'Placeholder: Professional Title',
            'biography' => 'Placeholder: Add your biography in the admin dashboard.',
            'email' => 'placeholder@example.test',
            'availability' => 'Placeholder: Add your availability.',
            'seo_title' => 'Placeholder: Developer Portfolio',
            'seo_description' => 'Placeholder: Add a concise description of your portfolio.',
        ]);
    }
}
