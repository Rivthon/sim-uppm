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
        Schema::create('grant_periods', function (Blueprint $table) {
            $table->id();
            $table->foreignId('academic_year_id')->constrained()->restrictOnDelete();
            $table->foreignId('grant_scheme_id')->constrained()->restrictOnDelete();
            $table->string('name');
            $table->dateTime('submission_starts_at');
            $table->dateTime('submission_ends_at');
            $table->unsignedSmallInteger('quota')->nullable();
            $table->unsignedBigInteger('budget')->default(0);
            $table->text('requirements')->nullable();
            $table->string('status', 20)->default('draft');
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();
            $table->timestamps();
            $table->index(['status', 'submission_starts_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('grant_periods');
    }
};
