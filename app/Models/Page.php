<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Page extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title', 'slug', 'banner_image', 'short_intro', 'content',
        'sidebar_content', 'template', 'parent_id', 'sort_order',
        'status', 'published_at', 'is_system',
    ];

    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
            'is_system' => 'boolean',
        ];
    }

    public function seo()
    {
        return $this->hasOne(PageSeo::class);
    }

    public function revisions()
    {
        return $this->hasMany(PageRevision::class)->latest();
    }

    public function parent()
    {
        return $this->belongsTo(Page::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Page::class, 'parent_id');
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published')
            ->where(fn ($q) => $q->whereNull('published_at')->orWhere('published_at', '<=', now()));
    }
}
