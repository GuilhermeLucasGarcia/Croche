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
        Schema::create('CATEGORIA', function (Blueprint $table) {
            $table->id();
            $table->string('NOME');
            $table->string('slug')->unique()->nullable();
            $table->text('DESCRICAO')->nullable();
            $table->string('IMG_URL')->nullable();
            $table->unsignedBigInteger('CATEGORIA_PAI_ID')->nullable();
            $table->boolean('ATIVO')->default(true);
            $table->timestamp('DT_CRIACAO')->nullable();
            $table->timestamp('DT_ALTERACAO')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('CATEGORIA');
    }
};
