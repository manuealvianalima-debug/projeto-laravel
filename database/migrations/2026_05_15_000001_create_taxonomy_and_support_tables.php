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
        if (! Schema::hasTable('papeis')) {
            Schema::create('papeis', function (Blueprint $table) {
                $table->id();
                $table->string('nome')->unique();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('unidades')) {
            Schema::create('unidades', function (Blueprint $table) {
                $table->id();
                $table->string('nome')->unique();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('nits')) {
            Schema::create('nits', function (Blueprint $table) {
                $table->id();
                $table->string('nome')->unique();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('diferenciais')) {
            Schema::create('diferenciais', function (Blueprint $table) {
                $table->id();
                $table->string('nome');
                $table->string('icone')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('palavras_chave')) {
            Schema::create('palavras_chave', function (Blueprint $table) {
                $table->id();
                $table->string('nome');
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('doencas')) {
            Schema::create('doencas', function (Blueprint $table) {
                $table->id();
                $table->string('nome');
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('categorias')) {
            Schema::create('categorias', function (Blueprint $table) {
                $table->id();
                $table->string('nome');
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('estagios')) {
            Schema::create('estagios', function (Blueprint $table) {
                $table->id();
                $table->string('nome');
                $table->string('etapa')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('situacoes')) {
            Schema::create('situacoes', function (Blueprint $table) {
                $table->id();
                $table->string('nome');
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('midias')) {
            Schema::create('midias', function (Blueprint $table) {
                $table->id();
                $table->foreignId('tecnologia_id')->constrained('tecnologias')->cascadeOnDelete();
                $table->text('url_imagem')->nullable();
                $table->text('url_video')->nullable();
                $table->longText('descricao')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('propriedades_intelectuais')) {
            Schema::create('propriedades_intelectuais', function (Blueprint $table) {
                $table->id();
                $table->foreignId('tecnologia_id')->constrained('tecnologias')->cascadeOnDelete();
                $table->string('tipo')->nullable();
                $table->longText('descricao')->nullable();
                $table->string('link')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('inventores')) {
            Schema::create('inventores', function (Blueprint $table) {
                $table->id();
                $table->foreignId('tecnologia_id')->constrained('tecnologias')->cascadeOnDelete();
                $table->string('nome');
                $table->boolean('coordenador')->default(false);
                $table->string('lattes')->nullable();
                $table->string('linkedin')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('diferencial_tecnologia')) {
            Schema::create('diferencial_tecnologia', function (Blueprint $table) {
                $table->id();
                $table->foreignId('tecnologia_id')->constrained('tecnologias')->cascadeOnDelete();
                $table->foreignId('diferencial_id')->constrained('diferenciais')->cascadeOnDelete();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('palavra_chave_tecnologia')) {
            Schema::create('palavra_chave_tecnologia', function (Blueprint $table) {
                $table->id();
                $table->foreignId('tecnologia_id')->constrained('tecnologias')->cascadeOnDelete();
                $table->foreignId('palavra_chave_id')->constrained('palavras_chave')->cascadeOnDelete();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('doenca_tecnologia')) {
            Schema::create('doenca_tecnologia', function (Blueprint $table) {
                $table->id();
                $table->foreignId('tecnologia_id')->constrained('tecnologias')->cascadeOnDelete();
                $table->foreignId('doenca_id')->constrained('doencas')->cascadeOnDelete();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('categoria_tecnologia')) {
            Schema::create('categoria_tecnologia', function (Blueprint $table) {
                $table->id();
                $table->foreignId('tecnologia_id')->constrained('tecnologias')->cascadeOnDelete();
                $table->foreignId('categoria_id')->constrained('categorias')->cascadeOnDelete();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categoria_tecnologia');
        Schema::dropIfExists('doenca_tecnologia');
        Schema::dropIfExists('palavra_chave_tecnologia');
        Schema::dropIfExists('diferencial_tecnologia');
        Schema::dropIfExists('inventores');
        Schema::dropIfExists('propriedades_intelectuais');
        Schema::dropIfExists('midias');
        Schema::dropIfExists('situacoes');
        Schema::dropIfExists('estagios');
        Schema::dropIfExists('categorias');
        Schema::dropIfExists('doencas');
        Schema::dropIfExists('palavras_chave');
        Schema::dropIfExists('diferenciais');
        Schema::dropIfExists('nits');
        Schema::dropIfExists('unidades');
        Schema::dropIfExists('papeis');
    }
};
