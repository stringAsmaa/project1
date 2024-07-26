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
        Schema::create('locations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete()->cascadeOnUpdate();
           $table->enum('name',[
            'Damascus',
            'Aleppo',
            'Homs',
            'Hama',
            'Latakia',
            'Tartus',
            'Deir ez-Zor',
            'Raqqa',
            'Daraa',
            'Idlib',
            ' Al-Hasakah',
            'Quneitra',
            ' Rif Dimashq',
            ' Suwayda',
           ]);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('locations');
    }
};
