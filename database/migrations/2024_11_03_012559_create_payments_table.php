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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payment_statu_id')->default(2)->constrained('payment_status')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('token_id')->default(2)->constrained('tokens')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('wallet_id')->default(2)->constrained('wallets')->onUpdate('cascade')->onDelete('cascade');
            $table->decimal('amount', 12, 2)->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
