<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('consultation', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained('patient')->cascadeOnDelete();
            $table->foreignId('medecin_id')->constrained('medecin')->cascadeOnDelete();
            $table->date('date_consultation');
            $table->text('motif');
            $table->text('diagnostic');
            $table->text('observation')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('consultation');
    }
};
