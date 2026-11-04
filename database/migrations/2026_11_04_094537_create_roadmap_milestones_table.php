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
        Schema::create('roadmap_milestones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('strategic_plan_id')->constrained('strategic_roadmaps')->cascadeOnDelete();
            $table->unsignedSmallInteger('start_year');
            $table->unsignedSmallInteger('end_year');
            $table->string('theme');
            $table->text('focus');
            $table->text('indicators');
            $table->text('target_outputs')->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();
            $table->timestamps();
            $table->index(['strategic_plan_id', 'sort_order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('roadmap_milestones');
    }
};
