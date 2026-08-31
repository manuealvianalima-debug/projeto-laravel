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
        Schema::create('tecnologias', function (Blueprint $table) {
            $table->id();
            $table->string('titulo');
            $table->string('nome')->nullable();
            $table->string('idioma')->nullable();
            $table->unsignedBigInteger('nit_relacionado_id')->nullable();
            $table->string('numero_caso')->unique();
            $table->date('data_submissao');
            $table->longText('resumo_solucao');
            $table->longText('problema');
            $table->longText('solucao');
            $table->longText('o_que_buscam')->nullable();
            $table->longText('busca')->nullable();
            $table->unsignedBigInteger('estagio_id')->nullable();
            $table->unsignedBigInteger('situacao_id')->nullable();
            $table->date('data_publicacao')->nullable();
            $table->boolean('possui_pi')->default(false);
            $table->longText('anotacoes_gestec')->nullable();
            $table->longText('anotacoes_icict')->nullable();
            $table->string('imagem_lateral')->nullable();
            $table->string('slug')->unique();
            $table->integer('drupal_nid')->unique();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tecnologias');
    }
};