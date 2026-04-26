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
        Schema::table('ammunitions', function (Blueprint $table) {
            $table->unsignedInteger('cart_quantity_step')->default(10)->after('standard_price');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ammunitions', function (Blueprint $table) {
            $table->dropColumn('cart_quantity_step');
        });
    }
};
