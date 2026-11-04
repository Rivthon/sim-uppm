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
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('funding_contract_id')->constrained()->cascadeOnDelete();
            $table->date('activity_date');
            $table->text('description');
            $table->text('result');
            $table->unsignedTinyInteger('progress_percentage');
            $table->unsignedBigInteger('expense_amount')->default(0);
            $table->string('attachment_path')->nullable();
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();
            $table->timestamps();
            $table->index(['funding_contract_id', 'activity_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
