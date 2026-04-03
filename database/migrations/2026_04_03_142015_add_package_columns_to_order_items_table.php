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
        Schema::table('order_items', function (Blueprint $table) {
            $table->unsignedBigInteger('gun_package_id')->nullable()->after('ammunition_id');
            $table->string('gun_package_name')->nullable()->after('ammunition_name');
            $table->text('gun_package_guns_summary')->nullable()->after('gun_package_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn([
                'gun_package_id',
                'gun_package_name',
                'gun_package_guns_summary',
            ]);
        });
    }
};
