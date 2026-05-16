<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('pessoa_activity_logs')) {
            return;
        }

        Schema::create('pessoa_activity_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pessoa_id')->index();
            $table->string('action', 120);
            $table->text('description');
            $table->json('metadata')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pessoa_activity_logs');
    }
};
