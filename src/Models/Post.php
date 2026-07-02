<?php

declare(strict_types=1);

namespace CmsOrbit\Blog\Models;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $fillable = [
        'title',
        'body',
    ];
}
