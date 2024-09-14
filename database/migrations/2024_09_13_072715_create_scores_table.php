<?php

use App\Enums\Calibre;
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
        Schema::create('scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->constrained('members');
            $table->string('member_type');
            $table->foreignId('shoot_id')->constrained('shoots');
            $table->foreignId('class_category_id')->constrained('class_categories');
            $table->integer('score')->default(0);
            $table->enum('calibre', array_column(Calibre::cases(), 'value'))
                ->default(Calibre::ONE_SEVEN_SEVEN->value);
            $table->dateTimeTz('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->dateTimeTz('updated_at')
                ->default(DB::raw('CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP'));
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scores');
    }
};
