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
        Schema::create('proposals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('grant_period_id')->constrained()->restrictOnDelete();
            $table->foreignId('research_field_id')->constrained()->restrictOnDelete();
            $table->foreignId('lead_id')->constrained('users')->restrictOnDelete();
            $table->string('code')->nullable()->unique();
            $table->string('title');
            $table->string('type', 20);
            $table->text('abstract');
            $table->text('background');
            $table->text('objectives');
            $table->text('methodology');
            $table->text('roadmap_alignment');
            $table->string('partner_name')->nullable();
            $table->string('partner_location')->nullable();
            $table->unsignedBigInteger('requested_budget');
            $table->unsignedTinyInteger('duration_months');
            $table->string('document_path')->nullable();
            $table->string('status', 20)->default('draft');
            $table->timestamp('submitted_at')->nullable();
            $table->timestamps();
            $table->index(['lead_id', 'status', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('proposals');
    }
};
