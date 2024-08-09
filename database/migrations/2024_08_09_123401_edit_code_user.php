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
        schema::table('code_users', function (Blueprint $table) {
            $table->integer('product_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        schema::table('code_users', function (Blueprint $table) {
            $table->dropColumn('product_id');
        });
    }
};
