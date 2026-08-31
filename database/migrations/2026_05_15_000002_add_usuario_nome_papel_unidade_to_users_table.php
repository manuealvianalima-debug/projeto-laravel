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
            $table->string('usuario')->after('id')->nullable();
            $table->string('nome')->after('usuario')->nullable();
            $table->foreignId('papel_id')->nullable()->after('nome')->constrained('papeis')->nullOnDelete();
            $table->foreignId('unidade_id')->nullable()->after('papel_id')->constrained('unidades')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['papel_id']);
            $table->dropForeign(['unidade_id']);
            $table->dropColumn(['usuario', 'nome', 'papel_id', 'unidade_id']);
        });
    }
};
