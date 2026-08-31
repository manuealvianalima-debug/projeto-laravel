<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tecnologias', function (Blueprint $table) {
            if (! Schema::hasColumn('tecnologias', 'nome')) {
                $table->string('nome')->after('id')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('tecnologias', function (Blueprint $table) {
            if (Schema::hasColumn('tecnologias', 'nome')) {
                $table->dropColumn('nome');
            }
        });
    }
};
