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
        Schema::create('guns', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('gun_type_id')->constrained()->onDelete('cascade');
            $table->foreignId('caliber_id')->constrained()->onDelete('cascade');
            $table->text('description')->nullable();
            $table->json('photos')->nullable(); // Array of photo paths
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('guns');
    }
};
