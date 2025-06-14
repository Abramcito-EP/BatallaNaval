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
        Schema::table('games', function (Blueprint $table) {
            $table->string('end_reason')->nullable()->after('winner_id');
            $table->integer('inactive_turns_count')->default(0)->after('end_reason');
            $table->timestamp('last_activity')->nullable()->after('inactive_turns_count');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('games', function (Blueprint $table) {
            $table->dropColumn(['end_reason', 'inactive_turns_count', 'last_activity']);
        });
    }
};