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
        Schema::table('proposal_members', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->after('proposal_id')->constrained()->nullOnDelete();
            $table->index(['identity_number', 'member_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('proposal_members', function (Blueprint $table) {
            $table->dropIndex(['identity_number', 'member_type']);
            $table->dropConstrainedForeignId('user_id');
        });
    }
};
