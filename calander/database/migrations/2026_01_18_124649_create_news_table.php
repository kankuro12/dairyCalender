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
        Schema::create('news', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('title_nep')->nullable();
            $table->text('excerpt')->nullable();
            $table->text('excerpt_nep')->nullable();
            $table->longText('content')->nullable();
            $table->longText('content_nep')->nullable();
            $table->string('image')->nullable();
            $table->string('source_url')->nullable();
            $table->string('source_name')->nullable();
            $table->json('sources')->nullable(); // For multiple sources with icons
            $table->enum('source_type', ['api', 'manual'])->default('manual');
            $table->boolean('status')->default(true);
            $table->integer('priority')->default(0);
            $table->timestamp('published_at')->nullable();
            $table->timestamps();

            $table->index('status');
            $table->index('priority');
            $table->index('published_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('news');
    }
};
