<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('PESSOA', function (Blueprint $table) {
            $table->id();
            $table->string('IMG_URL')->nullable();
            $table->string('NOME');
            $table->string('NOME_USUARIO')->unique();
            $table->string('SENHA');
            $table->string('PERFIL')->default('user');
            $table->string('EMAIL')->unique();
            $table->string('RESET_TOKEN_HASH')->nullable();
            $table->timestamp('RESET_TOKEN_EXPIRES')->nullable();
            $table->string('SENHA_ANTERIOR_1')->nullable();
            $table->string('SENHA_ANTERIOR_2')->nullable();
            $table->string('SENHA_ANTERIOR_3')->nullable();
            $table->timestamp('DT_ALTERACAO')->nullable();
            // The add_admin_fields_to_supabase_schema migration will add the ATIVO column.
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('PESSOA');
    }
};