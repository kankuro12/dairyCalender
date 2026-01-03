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
        Schema::create('calendar_events', function (Blueprint $table) {
            $table->id();

            $table->unsignedSmallInteger('bs_year');
            $table->unsignedTinyInteger('bs_month');
            $table->unsignedTinyInteger('bs_day');

            $table->date('ad_date')->index();
            $table->string('title');
            $table->boolean('is_holiday')->default(false);
            $table->enum('holiday_type', ['public', 'optional', 'other'])->nullable();
            $table->json('extra_events')->nullable();
            $table->text('notes')->nullable();


            $table->timestamps();

            $table->unique(['bs_year','bs_month','bs_day']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('calendar_events');
    }
};
