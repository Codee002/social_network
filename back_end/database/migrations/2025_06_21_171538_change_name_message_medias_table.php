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
        Schema::rename('message_medias', 'message_media');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::rename('message_media', 'message_medias');

    }
};
