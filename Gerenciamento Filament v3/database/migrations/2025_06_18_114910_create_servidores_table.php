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
        Schema::create('servidores', function (Blueprint $table) {
            $table->id();
            $table->string('matricula')->unique();
            $table->string('nome');
            $table->date('data_admissao');
            $table->string('email')->unique();
            $table->foreignId('cargo_id')->constrained()->nullOnDelete()->nullable();
            $table->foreignId('turno_id')->constrained()->nullOnDelete()->nullable();
            $table->foreignId('lotacao_id')->constrained('lotacoes')->nullOnDelete()->nullable();
            $table->timestamps();
        });


        Schema::table('servidores', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->nullable()->unique()->after('id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('restrict');
        });
    }



    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('servidores');
    }
};
