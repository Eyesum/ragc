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
        Schema::create('membership_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('payment_period', array_column(\App\Enums\PaymentPeriod::cases(), 'value'))
                ->default(\App\Enums\PaymentPeriod::ANNUAL->value);
            $table->integer('cost');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('membership_types');
    }
};
