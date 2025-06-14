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
        Schema::create('games', function (Blueprint $table) {
            $table->id();
            $table->foreignId('host_id')->constrained('users');
            $table->foreignId('guest_id')->nullable()->constrained('users');
            $table->enum('status', ['waiting', 'in_progress', 'finished'])->default('waiting');
            $table->enum('scenario', ['classic', 'foggy', 'stormy'])->default('classic');
            $table->integer('max_response_time')->default(30); // seconds
            $table->foreignId('winner_id')->nullable()->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('games');
    }
};