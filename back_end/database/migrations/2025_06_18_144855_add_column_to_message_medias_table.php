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
        Schema::table('message_medias', function (Blueprint $table) {
            $table->enum('type', ['image', 'video', 'file']);

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('message_medias', function (Blueprint $table) {
            $table->dropColumn("type");
        });
    }
};
