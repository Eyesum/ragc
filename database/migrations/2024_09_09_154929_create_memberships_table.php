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
        Schema::create('memberships', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('member_id');
            $table->string('member_type');
            $table->foreignId('membership_type_id')->constrained('membership_types');
            $table->date('joined_date');
            $table->enum('status', array_column(\App\Enums\MembershipStatus::cases(), 'value'))
                ->default(\App\Enums\MembershipStatus::ACTIVE->value);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('memberships');
    }
};
