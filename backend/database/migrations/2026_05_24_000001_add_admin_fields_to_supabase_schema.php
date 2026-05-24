<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('PRODUTO')) {
            if (! Schema::hasColumn('PRODUTO', 'ESTOQUE')) {
                Schema::table('PRODUTO', function (Blueprint $table) {
                    $table->integer('ESTOQUE')->default(0);
                });
            }

            if (! Schema::hasColumn('PRODUTO', 'ATIVO')) {
                Schema::table('PRODUTO', function (Blueprint $table) {
                    $table->boolean('ATIVO')->default(true);
                });
            }
        }

        if (Schema::hasTable('CATEGORIA') && ! Schema::hasColumn('CATEGORIA', 'CATEGORIA_PAI_ID')) {
            Schema::table('CATEGORIA', function (Blueprint $table) {
                $table->unsignedBigInteger('CATEGORIA_PAI_ID')->nullable();
            });
        }

        if (Schema::hasTable('PESSOA') && ! Schema::hasColumn('PESSOA', 'ATIVO')) {
            Schema::table('PESSOA', function (Blueprint $table) {
                $table->boolean('ATIVO')->default(true);
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('PRODUTO')) {
            if (Schema::hasColumn('PRODUTO', 'ESTOQUE')) {
                Schema::table('PRODUTO', function (Blueprint $table) {
                    $table->dropColumn('ESTOQUE');
                });
            }

            if (Schema::hasColumn('PRODUTO', 'ATIVO')) {
                Schema::table('PRODUTO', function (Blueprint $table) {
                    $table->dropColumn('ATIVO');
                });
            }
        }

        if (Schema::hasTable('CATEGORIA') && Schema::hasColumn('CATEGORIA', 'CATEGORIA_PAI_ID')) {
            Schema::table('CATEGORIA', function (Blueprint $table) {
                $table->dropColumn('CATEGORIA_PAI_ID');
            });
        }

        if (Schema::hasTable('PESSOA') && Schema::hasColumn('PESSOA', 'ATIVO')) {
            Schema::table('PESSOA', function (Blueprint $table) {
                $table->dropColumn('ATIVO');
            });
        }
    }
};

