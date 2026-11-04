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
        Schema::create('funding_disbursements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('funding_contract_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('sequence');
            $table->string('label');
            $table->unsignedTinyInteger('percentage');
            $table->unsignedBigInteger('amount');
            $table->date('scheduled_at');
            $table->date('paid_at')->nullable();
            $table->string('status', 20)->default('planned');
            $table->string('proof_path')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('recorded_by')->constrained('users')->restrictOnDelete();
            $table->timestamps();
            $table->unique(['funding_contract_id', 'sequence']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('funding_disbursements');
    }
};
