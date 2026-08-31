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
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'ativo')) {
                $table->boolean('ativo')->default(true)->after('unidade_id');
            }
            if (! Schema::hasColumn('users', 'ultimo_acesso')) {
                $table->timestamp('ultimo_acesso')->nullable()->after('ativo');
            }
            if (! Schema::hasColumn('users', 'descricao')) {
                $table->text('descricao')->nullable()->after('nome');
            }
        });

        Schema::table('categorias', function (Blueprint $table) {
            if (! Schema::hasColumn('categorias', 'nome')) {
                $table->string('nome')->nullable()->after('id');
            }
            if (! Schema::hasColumn('categorias', 'descricao')) {
                $table->text('descricao')->nullable()->after('nome');
            }
        });

        Schema::table('situacoes', function (Blueprint $table) {
            if (! Schema::hasColumn('situacoes', 'descricao')) {
                $table->text('descricao')->nullable()->after('nome');
            }
        });

        Schema::table('midias', function (Blueprint $table) {
            if (! Schema::hasColumn('midias', 'tipo_midia')) {
                $table->enum('tipo_midia', ['imagem', 'video', 'ambos'])->default('imagem')->after('descricao');
            }
            if (! Schema::hasColumn('midias', 'ordem')) {
                $table->integer('ordem')->default(0)->after('tipo_midia');
            }
        });

        Schema::table('propriedades_intelectuais', function (Blueprint $table) {
            if (! Schema::hasColumn('propriedades_intelectuais', 'possui_propriedade')) {
                $table->boolean('possui_propriedade')->default(false)->after('tecnologia_id');
            }
            if (! Schema::hasColumn('propriedades_intelectuais', 'tipo_propriedade_id')) {
                $table->foreignId('tipo_propriedade_id')->nullable()->after('possui_propriedade')->constrained('tipo_propriedade')->nullOnDelete();
            }
            if (! Schema::hasColumn('propriedades_intelectuais', 'link_propriedade')) {
                $table->string('link_propriedade', 500)->nullable()->after('descricao');
            }
            if (! Schema::hasColumn('propriedades_intelectuais', 'numero_registro')) {
                $table->string('numero_registro', 100)->nullable()->after('link_propriedade');
            }
            if (! Schema::hasColumn('propriedades_intelectuais', 'data_registro')) {
                $table->date('data_registro')->nullable()->after('numero_registro');
            }
        });

        Schema::table('inventores', function (Blueprint $table) {
            if (! Schema::hasColumn('inventores', 'email')) {
                $table->string('email', 200)->nullable()->after('linkedin');
            }
            if (! Schema::hasColumn('inventores', 'instituicao')) {
                $table->string('instituicao', 200)->nullable()->after('email');
            }
        });

        Schema::table('tecnologias', function (Blueprint $table) {
            if (! Schema::hasColumn('tecnologias', 'id_user_criador')) {
                $table->foreignId('id_user_criador')->nullable()->after('situacao_id')->constrained('users')->nullOnDelete();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tecnologias', function (Blueprint $table) {
            if (Schema::hasColumn('tecnologias', 'id_user_criador')) {
                $table->dropConstrainedForeignId('id_user_criador');
            }
        });

        Schema::table('inventores', function (Blueprint $table) {
            if (Schema::hasColumn('inventores', 'instituicao')) {
                $table->dropColumn('instituicao');
            }
            if (Schema::hasColumn('inventores', 'email')) {
                $table->dropColumn('email');
            }
        });

        Schema::table('propriedades_intelectuais', function (Blueprint $table) {
            if (Schema::hasColumn('propriedades_intelectuais', 'data_registro')) {
                $table->dropColumn('data_registro');
            }
            if (Schema::hasColumn('propriedades_intelectuais', 'numero_registro')) {
                $table->dropColumn('numero_registro');
            }
            if (Schema::hasColumn('propriedades_intelectuais', 'link_propriedade')) {
                $table->dropColumn('link_propriedade');
            }
            if (Schema::hasColumn('propriedades_intelectuais', 'tipo_propriedade_id')) {
                $table->dropConstrainedForeignId('tipo_propriedade_id');
            }
            if (Schema::hasColumn('propriedades_intelectuais', 'possui_propriedade')) {
                $table->dropColumn('possui_propriedade');
            }
        });

        Schema::table('midias', function (Blueprint $table) {
            if (Schema::hasColumn('midias', 'ordem')) {
                $table->dropColumn('ordem');
            }
            if (Schema::hasColumn('midias', 'tipo_midia')) {
                $table->dropColumn('tipo_midia');
            }
        });

        Schema::table('situacoes', function (Blueprint $table) {
            if (Schema::hasColumn('situacoes', 'descricao')) {
                $table->dropColumn('descricao');
            }
        });

        Schema::table('categorias', function (Blueprint $table) {
            if (Schema::hasColumn('categorias', 'descricao')) {
                $table->dropColumn('descricao');
            }
            if (Schema::hasColumn('categorias', 'nome')) {
                $table->dropColumn('nome');
            }
        });

        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'descricao')) {
                $table->dropColumn('descricao');
            }
            if (Schema::hasColumn('users', 'ultimo_acesso')) {
                $table->dropColumn('ultimo_acesso');
            }
            if (Schema::hasColumn('users', 'ativo')) {
                $table->dropColumn('ativo');
            }
        });
    }
};
