<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Criar tabela tipo_propriedade
        if (! Schema::hasTable('tipo_propriedade')) {
            Schema::create('tipo_propriedade', function (Blueprint $table) {
                $table->id();
                $table->string('nome')->unique();
                $table->text('descricao')->nullable();
                $table->timestamps();
            });
        }

        // Criar tabela anotacoes
        if (! Schema::hasTable('anotacoes')) {
            Schema::create('anotacoes', function (Blueprint $table) {
                $table->id();
                $table->foreignId('tecnologia_id')->constrained('tecnologias')->cascadeOnDelete();
                $table->longText('anotacao_gestec')->nullable();
                $table->longText('anotacao_icict')->nullable();
                $table->foreignId('id_user_gestec')->nullable()->constrained('users')->nullOnDelete();
                $table->foreignId('id_user_icict')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamp('data_anotacao_gestec')->nullable();
                $table->timestamp('data_anotacao_icict')->nullable();
                $table->timestamps();
                $table->unique(['tecnologia_id']);
            });
        }

        // Modificar tabela users
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

        // Modificar tabela tecnologias
        Schema::table('tecnologias', function (Blueprint $table) {
            if (! Schema::hasColumn('tecnologias', 'idioma')) {
                $table->string('idioma', 10)->default('pt-br')->after('titulo');
            }

            if (! Schema::hasColumn('tecnologias', 'id_user_criador')) {
                $table->foreignId('id_user_criador')
                    ->nullable()
                    ->after('situacao_id')
                    ->constrained('users')
                    ->nullOnDelete();
            }

            if (! Schema::hasColumn('tecnologias', 'id_status')) {
                $table->unsignedBigInteger('id_status')->nullable()->after('situacao_id');
                $table->foreign('id_status')->references('id')->on('situacoes')->nullOnDelete();
            }

            if (! $this->indexExists('tecnologias', 'idx_tec_status')) {
                $table->index('id_status', 'idx_tec_status');
            }

            if (Schema::hasColumn('tecnologias', 'data_publicacao') &&
                ! $this->indexExists('tecnologias', 'idx_tec_data')) {
                $table->index('data_publicacao', 'idx_tec_data');
            }

            if (Schema::hasColumn('tecnologias', 'numero_caso') &&
                ! $this->indexExists('tecnologias', 'idx_tec_numero_caso')) {
                $table->index('numero_caso', 'idx_tec_numero_caso');
            }
        });

        // Modificar tabela Papeis
        Schema::table('papeis', function (Blueprint $table) {
            if (! Schema::hasColumn('papeis', 'descricao')) {
                $table->text('descricao')->nullable()->after('nome');
            }
        });

        // Modificar tabela unidades
        Schema::table('unidades', function (Blueprint $table) {
            if (! Schema::hasColumn('unidades', 'sigla')) {
                $table->string('sigla', 20)->nullable()->after('nome');
            }
        });

        // Modificar tabela NITs
        Schema::table('nits', function (Blueprint $table) {
            if (! Schema::hasColumn('nits', 'descricao')) {
                $table->text('descricao')->nullable()->after('nome');
            }
        });

        // Modificar tabela diferenciais
        Schema::table('diferenciais', function (Blueprint $table) {
            if (! Schema::hasColumn('diferenciais', 'descricao')) {
                $table->text('descricao')->nullable()->after('icone');
            }
        });

        // Modificar tabela categorias
        Schema::table('categorias', function (Blueprint $table) {
            if (! Schema::hasColumn('categorias', 'nome')) {
                $table->string('nome')->after('id');
            }
            if (! Schema::hasColumn('categorias', 'descricao')) {
                $table->text('descricao')->nullable()->after('nome');
            }
        });

        // Modificar tabela doenças
        Schema::table('doencas', function (Blueprint $table) {
            if (! Schema::hasColumn('doencas', 'cid')) {
                $table->string('cid', 10)->nullable()->after('nome');
            }
        });

        /* Modificar tabela estágios
        Schema::table('estagios', function (Blueprint $table) {
            if (! Schema::hasColumn('estagios', 'descricao')) {
                $table->text('descricao')->nullable()->after('etapa');
            }
        });
*/
        // Modificar tabela situações
        Schema::table('situacoes', function (Blueprint $table) {
            if (! Schema::hasColumn('situacoes', 'descricao')) {
                $table->text('descricao')->nullable()->after('nome');
            }
        });

        // Modificar tabela mídias
        Schema::table('midias', function (Blueprint $table) {
            if (! Schema::hasColumn('midias', 'tipo_midia')) {
                $table->enum('tipo_midia', ['imagem', 'video', 'ambos'])->default('imagem')->after('descricao');
            }
            if (! Schema::hasColumn('midias', 'ordem')) {
                $table->integer('ordem')->default(0)->after('tipo_midia');
            }
        });

        // Modificar tabela propriedades_intelectuais
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

        // Modificar tabela inventores
        Schema::table('inventores', function (Blueprint $table) {
            if (! Schema::hasColumn('inventores', 'email')) {
                $table->string('email', 200)->nullable()->after('linkedin');
            }
            if (! Schema::hasColumn('inventores', 'instituicao')) {
                $table->string('instituicao', 200)->nullable()->after('email');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reverter inventores
        Schema::table('inventores', function (Blueprint $table) {
            if (Schema::hasColumn('inventores', 'instituicao')) {
                $table->dropColumn('instituicao');
            }
            if (Schema::hasColumn('inventores', 'email')) {
                $table->dropColumn('email');
            }
        });

        // Reverter propriedades_intelectuais
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

        // Reverter midias
        Schema::table('midias', function (Blueprint $table) {
            if (Schema::hasColumn('midias', 'ordem')) {
                $table->dropColumn('ordem');
            }
            if (Schema::hasColumn('midias', 'tipo_midia')) {
                $table->dropColumn('tipo_midia');
            }
        });

        // Reverter situacoes
        Schema::table('situacoes', function (Blueprint $table) {
            if (Schema::hasColumn('situacoes', 'descricao')) {
                $table->dropColumn('descricao');
            }
        });

        // Reverter doenças
        Schema::table('doencas', function (Blueprint $table) {
            if (Schema::hasColumn('doencas', 'cid')) {
                $table->dropColumn('cid');
            }
        });

        // Reverter categorias
        Schema::table('categorias', function (Blueprint $table) {
            if (Schema::hasColumn('categorias', 'descricao')) {
                $table->dropColumn('descricao');
            }
        });

        // Reverter diferenciais
        Schema::table('diferenciais', function (Blueprint $table) {
            if (Schema::hasColumn('diferenciais', 'descricao')) {
                $table->dropColumn('descricao');
            }
        });

        // Reverter nits
        Schema::table('nits', function (Blueprint $table) {
            if (Schema::hasColumn('nits', 'descricao')) {
                $table->dropColumn('descricao');
            }
        });

        // Reverter unidades
        Schema::table('unidades', function (Blueprint $table) {
            if (Schema::hasColumn('unidades', 'sigla')) {
                $table->dropColumn('sigla');
            }
        });

        // Reverter papéis
        Schema::table('papeis', function (Blueprint $table) {
            if (Schema::hasColumn('papeis', 'descricao')) {
                $table->dropColumn('descricao');
            }
        });

        // Reverter tecnologias
        Schema::table('tecnologias', function (Blueprint $table) {
            if (Schema::hasColumn('tecnologias', 'idioma')) {
                $table->dropColumn('idioma');
            }
            if (Schema::hasColumn('tecnologias', 'id_user_criador')) {
                $table->dropConstrainedForeignId('id_user_criador');
            }
            if (Schema::hasColumn('tecnologias', 'id_status')) {
                $table->dropForeign(['id_status']);
                $table->dropColumn('id_status');
            }
        });

        // Reverter users
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

        // Remover tabelas
        Schema::dropIfExists('anotacoes');
        Schema::dropIfExists('tipo_propriedade');
    }

    /**
     * Verifica se um índice já existe no banco de dados.
     */
    private function indexExists(string $table, string $indexName): bool
    {
        $result = DB::select("
            SELECT COUNT(1) as count
            FROM information_schema.statistics
            WHERE table_schema = DATABASE()
            AND table_name = ?
            AND index_name = ?
        ", [$table, $indexName]);

        return $result[0]->count > 0;
    }
};