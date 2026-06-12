<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('receptionniste', function (Blueprint $table) {
            $table->id();
            $table->foreignId('utilisateur_id')->constrained('utilisateurs')->cascadeOnDelete();
            $table->string('nom');
            $table->string('prenom');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('receptionniste');
    }
};
