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
        Schema::create('review_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('proposal_id')->constrained()->cascadeOnDelete();
            $table->foreignId('reviewer_id')->constrained('users')->restrictOnDelete();
            $table->foreignId('assigned_by')->constrained('users')->restrictOnDelete();
            $table->dateTime('due_at');
            $table->string('status', 20)->default('assigned');
            $table->unsignedTinyInteger('relevance_score')->nullable();
            $table->unsignedTinyInteger('methodology_score')->nullable();
            $table->unsignedTinyInteger('feasibility_score')->nullable();
            $table->unsignedTinyInteger('budget_score')->nullable();
            $table->unsignedTinyInteger('output_score')->nullable();
            $table->text('strengths')->nullable();
            $table->text('weaknesses')->nullable();
            $table->text('comments')->nullable();
            $table->string('recommendation', 20)->nullable();
            $table->boolean('has_conflict')->default(false);
            $table->text('conflict_notes')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamps();
            $table->unique(['proposal_id', 'reviewer_id']);
            $table->index(['reviewer_id', 'status', 'due_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('review_assignments');
    }
};
