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
        Schema::create('salaries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete()->cascadeOnUpdate();
            $table->enum('range', [
                'from 100k to 150k',
                'from 150k to 200k',
                'from 250k to 300k',
                'from 300k to 600k',
                'from 600k to 1M',
                'from 1M to 1.5M',
                'from 1.5M to 2M',
                'from 2M to 2.5M',
                'from 2.5M to 3M',
                'from 3M to 4M',
                'from 4M to 5M',
                'from 5M to 7M'
            ]);
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('salaries');
    }
};
