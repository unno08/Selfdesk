<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            if (!Schema::hasColumn('tickets', 'buyer_last_read_at')) {
                $table->dateTime('buyer_last_read_at')->nullable();
            }
            if (!Schema::hasColumn('tickets', 'seller_last_read_at')) {
                $table->dateTime('seller_last_read_at')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            if (Schema::hasColumn('tickets', 'buyer_last_read_at')) {
                $table->dropColumn('buyer_last_read_at');
            }
            if (Schema::hasColumn('tickets', 'seller_last_read_at')) {
                $table->dropColumn('seller_last_read_at');
            }
        });
    }
};
