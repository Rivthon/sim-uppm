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
        Schema::create('final_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('funding_contract_id')->unique()->constrained()->cascadeOnDelete();
            $table->text('summary');
            $table->text('conclusions');
            $table->text('recommendations')->nullable();
            $table->unsignedBigInteger('budget_realized');
            $table->string('document_path');
            $table->string('status')->default('submitted');
            $table->text('verification_notes')->nullable();
            $table->timestamp('submitted_at');
            $table->foreignId('submitted_by')->constrained('users')->restrictOnDelete();
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('final_reports');
    }
};
