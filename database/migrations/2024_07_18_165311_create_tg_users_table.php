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
        Schema::create('tg_users', function (Blueprint $table) {
            $table->id();
            $table->integer('code_id')->nullable();
            $table->string('telegram_id')->unique();
            $table->string('username')->nullable();
            $table->string('phone')->nullable();
            $table->string('name')->nullable();
            $table->string('state')->nullable();
            $table->boolean('is_finished')->nullable();
            $table->string('last_message_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tg_users');
    }
};
