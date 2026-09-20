<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PageSeo extends Model
{
    protected $table = 'page_seo';

    protected $fillable = [
        'page_id', 'meta_title', 'meta_description', 'meta_keywords', 'canonical_url',
        'robots_index', 'robots_follow', 'og_title', 'og_description', 'og_image',
        'twitter_card_type', 'twitter_title', 'twitter_image', 'schema_jsonld',
        'custom_head_code', 'custom_footer_code',
    ];

    protected function casts(): array
    {
        return [
            'robots_index' => 'boolean',
            'robots_follow' => 'boolean',
        ];
    }

    public function page()
    {
        return $this->belongsTo(Page::class);
    }
}
