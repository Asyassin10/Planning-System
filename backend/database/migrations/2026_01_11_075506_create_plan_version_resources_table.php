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
        Schema::create('plan_version_resources', function (Blueprint $table) {
            $table->id();
            $table->foreignId('operational_plan_version_id')->constrained()->onDelete('cascade');
            $table->foreignId('resource_id')->constrained();
            $table->integer('capacity');
            $table->boolean('is_permanent')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('operational_plan_version_id');
            $table->index('resource_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plan_version_resources');
    }
};
