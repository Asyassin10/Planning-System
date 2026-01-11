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
        Schema::create('operational_plan_versions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('operational_plan_id')->constrained()->onDelete('cascade');
            $table->integer('version');
            $table->boolean('is_active')->default(false);
            $table->date('valid_from');
            $table->date('valid_to');
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();

            $table->unique(['operational_plan_id', 'version']);
            $table->index(['operational_plan_id', 'is_active']);
            $table->index(['valid_from', 'valid_to']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('operational_plan_versions');
    }
};
