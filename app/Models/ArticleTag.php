<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ArticleTag extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'meta_title',
        'description',
        'slug',
    ];

    public function articles(): BelongsToMany
    {
        return $this->belongsToMany(Article::class, 'article_has_tags', 'tag_id', 'article_id')
            ->withTimestamps();
    }
}
