<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('pessoa_notification_preferences')) {
            return;
        }

        Schema::create('pessoa_notification_preferences', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pessoa_id')->unique();
            $table->boolean('email_enabled')->default(true);
            $table->boolean('push_enabled')->default(false);
            $table->boolean('sms_enabled')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pessoa_notification_preferences');
    }
};
