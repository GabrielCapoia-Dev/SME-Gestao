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
            $table->integer('entidade');
            $table->bigInteger('matricula')->unique();
            $table->string('nome');
            $table->string('descricaoCargo')->nullable();
            $table->string('descricaoLotacao')->nullable();
            $table->string('descricaoClasse')->nullable();
            $table->string('descricaoNatureza')->nullable();
            $table->string('faixa')->nullable();
            $table->string('situacao')->nullable();
            $table->date('dataAdmissao')->nullable();
            $table->date('dataDemissao')->nullable();
            $table->string('localTrabalho')->nullable();

            $table->time('horarioEntrada')->nullable();
            $table->time('horarioSaidaIntervalo')->nullable();
            $table->time('horarioEntradaIntervalo')->nullable();
            $table->time('horarioSaida')->nullable();

            $table->string('horarioEspecial', 1)->nullable();
            $table->string('horarioTrabalho')->nullable();

            $table->string('horarioSaidaFormated')->nullable();
            $table->string('horarioEntradaFormated')->nullable();
            $table->string('horarioSaidaIntervaloFormated')->nullable();
            $table->string('horarioEntradaIntervaloFormated')->nullable();

            $table->timestamps();
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
