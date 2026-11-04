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
        Schema::create('progress_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('funding_contract_id')->constrained()->cascadeOnDelete();
            $table->date('reporting_period');
            $table->unsignedTinyInteger('progress_percentage');
            $table->text('summary');
            $table->text('achievements');
            $table->text('obstacles')->nullable();
            $table->unsignedBigInteger('budget_realized')->default(0);
            $table->text('output_progress');
            $table->string('document_path')->nullable();
            $table->string('status', 20)->default('submitted');
            $table->timestamp('submitted_at')->nullable();
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();
            $table->timestamps();
            $table->unique(['funding_contract_id', 'reporting_period']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('progress_reports');
    }
};
