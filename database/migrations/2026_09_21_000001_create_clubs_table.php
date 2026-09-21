<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Minimal stub so club_id foreign keys and tenant scoping have a real
// table to reference. The full Club module (Razorpay onboarding, KYC,
// stakeholders) is built in Phase 2 — this only carries what Phase 1's
// architecture needs to exist.
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clubs', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('status')->default('pending'); // pending / active / suspended / expired
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clubs');
    }
};
