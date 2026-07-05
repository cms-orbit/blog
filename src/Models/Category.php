<?php

declare(strict_types=1);

namespace CmsOrbit\Blog\Models;

use CmsOrbit\Blog\Database\Factories\CategoryFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Category extends Model
{
    /** @use HasFactory<CategoryFactory> */
    use HasFactory;
    protected $fillable = [
        'name',
        'slug',
        'description',
        'sort',
    ];

    protected static function booted(): void
    {
        static::creating(function (Category $category): void {
            if (blank($category->slug) && filled($category->name)) {
                $category->slug = Str::slug((string) $category->name) ?: 'category';
            }
        });
    }

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }
}
