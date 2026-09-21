<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MenuItem extends Model
{
    protected $fillable = [
        'menu_id', 'parent_id', 'label', 'linkable_type', 'linkable_id',
        'custom_url', 'target', 'visibility', 'sort_order',
    ];

    public function menu()
    {
        return $this->belongsTo(Menu::class);
    }

    public function parent()
    {
        return $this->belongsTo(MenuItem::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(MenuItem::class, 'parent_id')->orderBy('sort_order');
    }

    public function linkable()
    {
        return $this->morphTo();
    }

    public function resolvedUrl(): string
    {
        if ($this->custom_url) {
            return $this->custom_url;
        }

        if ($this->linkable_type === Page::class && $this->linkable) {
            return url('/'.$this->linkable->slug);
        }

        return '#';
    }
}
