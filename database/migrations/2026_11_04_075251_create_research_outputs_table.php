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
        Schema::create('research_outputs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('funding_contract_id')->constrained()->cascadeOnDelete();
            $table->string('type');
            $table->string('title');
            $table->string('achievement_status');
            $table->string('identifier')->nullable();
            $table->string('publisher')->nullable();
            $table->date('publication_date')->nullable();
            $table->string('url', 2048)->nullable();
            $table->string('evidence_path');
            $table->text('notes')->nullable();
            $table->string('verification_status')->default('pending');
            $table->text('verification_notes')->nullable();
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();
            $table->index(['funding_contract_id', 'type']);
            $table->index(['verification_status', 'type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('research_outputs');
    }
};
