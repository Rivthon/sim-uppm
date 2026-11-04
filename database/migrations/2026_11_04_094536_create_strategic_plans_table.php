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
        Schema::create('strategic_roadmaps', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('scope');
            $table->foreignId('study_program_id')->nullable()->constrained()->nullOnDelete();
            $table->string('type');
            $table->unsignedSmallInteger('start_year');
            $table->unsignedSmallInteger('end_year');
            $table->text('vision');
            $table->text('objectives');
            $table->string('document_path')->nullable();
            $table->string('status')->default('draft');
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();
            $table->timestamps();
            $table->index(['status', 'start_year', 'end_year']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('strategic_roadmaps');
    }
};
