<?php

use App\Enums\OrderPaymentMethod;
use App\Enums\OrderPaymentStatus;
use App\Enums\OrderStatus;
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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('address');
            $table->string('email')->index();
            $table->string('status')->default(OrderStatus::PendingVerification->value);
            $table->string('payment_method')->default(OrderPaymentMethod::PayOnSite->value);
            $table->string('payment_status')->default(OrderPaymentStatus::Pending->value);
            $table->boolean('is_club_member')->default(false);
            $table->unsignedInteger('total_shots')->default(0);
            $table->decimal('total_amount', 10, 2)->default(0);
            $table->string('verification_code_hash')->nullable();
            $table->timestamp('verification_code_expires_at')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->string('download_token', 64)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
