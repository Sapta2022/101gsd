<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'type', 'news_type', 'title', 'slug', 'featured_image', 'excerpt',
        'content', 'author_name', 'pdf_attachment', 'external_source_link',
        'status', 'published_at',
    ];

    protected function casts(): array
    {
        return ['published_at' => 'datetime'];
    }

    public function seo()
    {
        return $this->hasOne(PostSeo::class);
    }

    public function categories()
    {
        return $this->belongsToMany(PostCategory::class);
    }

    public function tags()
    {
        return $this->belongsToMany(PostTag::class);
    }

    public function scopeBlog($query)
    {
        return $query->where('type', 'blog');
    }

    public function scopeNews($query)
    {
        return $query->where('type', 'news');
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published')
            ->where(fn ($q) => $q->whereNull('published_at')->orWhere('published_at', '<=', now()));
    }
}
