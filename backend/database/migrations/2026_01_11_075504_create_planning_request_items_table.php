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
        Schema::create('planning_request_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('planning_request_id')->constrained()->onDelete('cascade');
            $table->foreignId('route_id')->constrained();
            $table->integer('capacity');
            $table->date('start_date');
            $table->date('end_date');
            $table->timestamps();

            $table->index('planning_request_id');
            $table->index(['route_id', 'start_date', 'end_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('planning_request_items');
    }
};
