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
        Schema::create('PRODUTO', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('CATEGORIA_ID')->nullable();
            $table->unsignedBigInteger('MARCA_ID')->nullable();
            $table->string('CODIGO')->nullable();
            $table->text('DETALHES')->nullable();
            $table->boolean('DESTAQUE')->default(false);
            $table->string('IMG_URL')->nullable();
            $table->text('DESCRICAO')->nullable();
            $table->decimal('VALOR', 10, 2)->default(0);
            $table->integer('ESTOQUE')->default(0);
            $table->boolean('ATIVO')->default(true);
            $table->timestamp('DT_ALTERACAO')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('PRODUTO');
    }
};
