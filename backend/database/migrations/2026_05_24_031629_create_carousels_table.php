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
        Schema::create('CARROSSEL', function (Blueprint $table) {
            $table->id();
            $table->string('TITULO', 60);
            $table->string('DESCRICAO', 120)->nullable();
            $table->string('IMG_DESKTOP_URL');
            $table->string('IMG_MOBILE_URL');
            $table->string('LINK_DESTINO')->nullable();
            $table->boolean('ATIVO')->default(true);
            $table->integer('ORDEM')->default(0);
            $table->timestamp('DT_CRIACAO')->useCurrent();
            $table->timestamp('DT_ALTERACAO')->useCurrent()->useCurrentOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('CARROSSEL');
    }
};
