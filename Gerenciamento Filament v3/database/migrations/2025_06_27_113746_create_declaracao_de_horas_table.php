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
        Schema::create('declaracao_de_horas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('servidor_id')->constrained('servidores')->onDelete('cascade');
            $table->foreignId('turno_id')->constrained()->nullOnDelete()->nullable();
            $table->date('data');
            $table->date('hora_inicio')->nullable();
            $table->date('hora_fim')->nullable();
            $table->string('cid')->nullable();
            $table->string('carga_horaria')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('declaracao_de_horas');
    }
};
