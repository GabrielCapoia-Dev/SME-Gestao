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
        Schema::create('atestados', function (Blueprint $table) {
            $table->id();
            $table->string('quantidade_dias')->nullable();
            $table->date('data_inicio');
            $table->boolean('prazo_indeterminado')->default(false);
            $table->date('data_fim')->nullable();
            $table->string('cid')->nullable();
            $table->foreignId('servidor_id')->constrained('servidores')->onDelete('cascade');
            $table->foreignId('tipo_atestado_id')->constrained('tipo_atestados')->onDelete('cascade');
            $table->timestamps();
        });

        Schema::table('atestados', function (Blueprint $table) {
            $table->foreignId('substituto_id')
                ->nullable()
                ->constrained('servidores')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('atestados');
    }
};
