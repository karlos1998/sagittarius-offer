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
        Schema::create('gun_package_gun', function (Blueprint $table) {
            $table->id();
            $table->foreignId('gun_package_id')->constrained()->cascadeOnDelete();
            $table->foreignId('gun_id')->constrained()->cascadeOnDelete();
            $table->foreignId('ammunition_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('shots_quantity')->default(5);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->unique(['gun_package_id', 'gun_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gun_package_gun');
    }
};
