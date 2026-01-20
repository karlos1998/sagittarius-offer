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
        Schema::create('ammunitions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('caliber_id')->constrained()->onDelete('cascade');
            $table->decimal('club_price', 8, 2)->nullable();
            $table->decimal('standard_price', 8, 2)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ammunitions');
    }
};
