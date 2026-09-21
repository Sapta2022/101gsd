<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Shared table for Blog and News & Updates, distinguished by `type`.
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->string('type'); // blog, news
            $table->string('news_type')->nullable(); // News, Press Release (only used when type = news)
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('featured_image')->nullable();
            $table->text('excerpt')->nullable(); // blog excerpt / news summary
            $table->longText('content')->nullable();
            $table->string('author_name')->nullable();
            $table->string('pdf_attachment')->nullable(); // news/press-release only
            $table->string('external_source_link')->nullable(); // news only
            $table->string('status')->default('draft'); // draft, published, scheduled
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
