<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('page_seo', function (Blueprint $table) {
            $table->id();
            $table->foreignId('page_id')->constrained()->cascadeOnDelete();
            $table->string('meta_title')->nullable();
            $table->string('meta_description', 320)->nullable();
            $table->string('meta_keywords')->nullable();
            $table->string('canonical_url')->nullable();
            $table->boolean('robots_index')->default(true);
            $table->boolean('robots_follow')->default(true);
            $table->string('og_title')->nullable();
            $table->string('og_description', 320)->nullable();
            $table->string('og_image')->nullable();
            $table->string('twitter_card_type')->nullable();
            $table->string('twitter_title')->nullable();
            $table->string('twitter_image')->nullable();
            $table->longText('schema_jsonld')->nullable();
            $table->longText('custom_head_code')->nullable();
            $table->longText('custom_footer_code')->nullable();
            $table->timestamps();

            $table->unique('page_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('page_seo');
    }
};
