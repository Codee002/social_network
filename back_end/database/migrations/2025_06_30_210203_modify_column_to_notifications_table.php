<?php

use App\Models\User;
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
        Schema::table('notifications', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn("user_id");

            // Khóa ngoại
            $table->unsignedBigInteger('sender_id');
            $table->unsignedBigInteger('received_id');
            $table->foreign("sender_id")->references("id")->on("users")->onDelete("cascade");
            $table->foreign("received_id")->references("id")->on("users")->onDelete("cascade");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            $table->foreignIdFor(User::class)->constrained();
            $table->dropColumn("sender_id");
            $table->dropColumn("receiver_id");
        });
    }
};
