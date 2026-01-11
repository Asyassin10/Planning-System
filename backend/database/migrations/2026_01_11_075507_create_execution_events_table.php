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
        Schema::create('execution_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('operational_plan_version_id')->constrained()->onDelete('cascade');
            $table->string('event_type');
            $table->json('event_data')->nullable();
            $table->foreignId('recorded_by')->constrained('users');
            $table->timestamp('recorded_at');
            $table->timestamps();

            $table->index(['operational_plan_version_id', 'event_type']);
            $table->index('recorded_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('execution_events');
    }
};
