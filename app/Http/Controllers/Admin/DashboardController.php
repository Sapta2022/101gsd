<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $stats = [
            'pages' => Page::query()->count(),
            'published_pages' => Page::query()->published()->count(),
            'posts' => Post::query()->count(),
            'published_posts' => Post::query()->published()->count(),
        ];

        return view('admin.dashboard', [
            'stats' => $stats,
        ]);
    }
}
