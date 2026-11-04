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
        Schema::create('kpi_targets', function (Blueprint $table) {
            $table->id();
            $table->unsignedSmallInteger('year');
            $table->string('metric');
            $table->unsignedBigInteger('target_value');
            $table->string('unit', 30);
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();
            $table->timestamps();
            $table->unique(['year', 'metric']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kpi_targets');
    }
};
