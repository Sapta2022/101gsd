<?php

namespace App\Services;

use App\Facades\Settings;
use App\Models\Page;
use App\Models\Post;

/**
 * Resolves the meta tags for one request: page/post-level SEO first,
 * falling back to the global SEO settings. One instance is built per
 * request by the meta partial — see resources/views/partials/seo-meta.blade.php.
 */
class SeoService
{
    public function forPage(Page $page): array
    {
        $seo = $page->seo;

        return $this->resolve(
            title: $seo?->meta_title ?: $page->title,
            description: $seo?->meta_description ?: $page->short_intro,
            keywords: $seo?->meta_keywords,
            canonical: $seo?->canonical_url,
            robotsIndex: $seo?->robots_index ?? true,
            robotsFollow: $seo?->robots_follow ?? true,
            ogTitle: $seo?->og_title,
            ogDescription: $seo?->og_description,
            ogImage: $seo?->og_image ?: $page->banner_image,
        );
    }

    public function forPost(Post $post): array
    {
        $seo = $post->seo;

        return $this->resolve(
            title: $seo?->meta_title ?: $post->title,
            description: $seo?->meta_description ?: $post->excerpt,
            keywords: $seo?->meta_keywords,
            canonical: $seo?->canonical_url,
            robotsIndex: $seo?->robots_index ?? true,
            robotsFollow: $seo?->robots_follow ?? true,
            ogTitle: $seo?->og_title,
            ogDescription: $seo?->og_description,
            ogImage: $seo?->og_image ?: $post->featured_image,
        );
    }

    public function forGlobal(?string $pageTitle = null): array
    {
        return $this->resolve(
            title: $pageTitle,
            description: Settings::get('seo', 'default_meta_description'),
            keywords: Settings::get('seo', 'default_meta_keywords'),
            canonical: null,
            robotsIndex: true,
            robotsFollow: true,
            ogTitle: null,
            ogDescription: null,
            ogImage: Settings::get('seo', 'default_og_image'),
        );
    }

    protected function resolve(
        ?string $title,
        ?string $description,
        ?string $keywords,
        ?string $canonical,
        bool $robotsIndex,
        bool $robotsFollow,
        ?string $ogTitle,
        ?string $ogDescription,
        ?string $ogImage,
    ): array {
        $pattern = Settings::get('seo', 'meta_title_pattern', '{page} | '.Settings::get('general', 'site_name', '101GSD'));
        $resolvedTitle = $title ? str_replace('{page}', $title, $pattern) : Settings::get('general', 'site_name', '101GSD');

        return [
            'title' => $resolvedTitle,
            'description' => $description ?: Settings::get('seo', 'default_meta_description'),
            'keywords' => $keywords ?: Settings::get('seo', 'default_meta_keywords'),
            'canonical' => $canonical ?: url()->current(),
            'robots' => trim(($robotsIndex ? 'index' : 'noindex').', '.($robotsFollow ? 'follow' : 'nofollow')),
            'og_title' => $ogTitle ?: $resolvedTitle,
            'og_description' => $ogDescription ?: ($description ?: Settings::get('seo', 'default_meta_description')),
            'og_image' => $ogImage ?: Settings::get('seo', 'default_og_image'),
        ];
    }
}
