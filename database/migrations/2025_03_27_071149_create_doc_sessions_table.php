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
        Schema::create('doc_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('clinic_id');
            $table->foreignId('doctor_id');
            $table->date('date');
            $table->enum('availability',['upcoming','ongoing','finished'])->default('upcoming');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('doc_sessions');
    }
};
