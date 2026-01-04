<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ticket_messages', function (Blueprint $table) {
            // drop FK dulu (SQLite kadang-kadang cerewet)
            // Kalau migration asal guna constrained('users'), nama fk biasanya ticket_messages_sender_id_foreign
            // Try ini dulu:
            $table->dropForeign(['sender_id']);

            // jadikan nullable
            $table->foreignId('sender_id')
                ->nullable()
                ->change();

            // letak balik FK dengan nullOnDelete (bila user delete, sender jadi null)
            $table->foreign('sender_id')
                ->references('id')->on('users')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('ticket_messages', function (Blueprint $table) {
            $table->dropForeign(['sender_id']);

            $table->foreignId('sender_id')
                ->nullable(false)
                ->change();

            $table->foreign('sender_id')
                ->references('id')->on('users')
                ->cascadeOnDelete();
        });
    }
};
