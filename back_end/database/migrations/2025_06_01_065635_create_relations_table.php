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
        Schema::create('relations', function (Blueprint $table) {
            $table->id();

            // Khóa ngoại
            $table->unsignedBigInteger('sender_id');
            $table->unsignedBigInteger('received_id');
            $table->foreign("sender_id")->references("id")->on("users")->onDelete("cascade");
            $table->foreign("received_id")->references("id")->on("users")->onDelete("cascade");

            $table->enum("type", ['friend', 'block', 'follow']);
            $table->enum("status", ['pendding', 'completed'])->default("pendding");

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('relations');
    }
};
