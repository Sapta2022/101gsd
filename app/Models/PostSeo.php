<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PostSeo extends Model
{
    protected $table = 'post_seo';

    protected $fillable = [
        'post_id', 'meta_title', 'meta_description', 'meta_keywords', 'canonical_url',
        'robots_index', 'robots_follow', 'og_title', 'og_description', 'og_image',
    ];

    protected function casts(): array
    {
        return [
            'robots_index' => 'boolean',
            'robots_follow' => 'boolean',
        ];
    }

    public function post()
    {
        return $this->belongsTo(Post::class);
    }
}
