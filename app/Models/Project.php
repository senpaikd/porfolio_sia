<?php

namespace App\Models;

use App\Casts\NormalizedHttpUrl;
use App\Enums\ProjectStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class Project extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected static function booted(): void
    {
        static::creating(function (Project $project): void {
            $base = Str::slug($project->slug ?: $project->title);
            if ($base === '') {
                throw ValidationException::withMessages(['slug' => 'A readable slug is required.']);
            }

            $candidate = $base;
            $suffix = 2;
            while (static::query()->where('slug', $candidate)->exists()) {
                $candidate = $base.'-'.$suffix++;
            }
            $project->slug = $candidate;
        });
    }

    protected function casts(): array
    {
        return [
            'github_url' => NormalizedHttpUrl::class,
            'live_url' => NormalizedHttpUrl::class,
            'status' => ProjectStatus::class,
            'is_featured' => 'boolean',
            'is_published' => 'boolean',
            'completion_date' => 'date',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProjectImage::class)->orderBy('display_order');
    }

    public function technologies(): BelongsToMany
    {
        return $this->belongsToMany(Technology::class);
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true);
    }

    public function scopeFeatured(Builder $query): Builder
    {
        return $query->published()->where('is_featured', true);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('display_order')->orderByDesc('completion_date')->orderBy('title');
    }
}
