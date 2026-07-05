<?php

declare(strict_types=1);

namespace CmsOrbit\Blog\Models;

use CmsOrbit\Blog\Database\Factories\TagFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class Tag extends Model
{
    /** @use HasFactory<TagFactory> */
    use HasFactory;
    protected $fillable = [
        'name',
        'slug',
    ];

    protected static function booted(): void
    {
        static::creating(function (Tag $tag): void {
            if (blank($tag->slug) && filled($tag->name)) {
                $tag->slug = Str::slug((string) $tag->name) ?: 'tag';
            }
        });
    }

    public function posts(): BelongsToMany
    {
        return $this->belongsToMany(Post::class);
    }
}
