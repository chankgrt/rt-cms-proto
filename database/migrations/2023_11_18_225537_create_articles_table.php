<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('articles', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->json('content')->nullable();
            $table->string('slug');
            $table->foreignId('author_id');
            $table->foreignId('front_author_id');
            $table->foreignId('dossier_id')->nullable();
            $table->foreignId('category_id')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->boolean('is_active')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('articles');
    }
};
