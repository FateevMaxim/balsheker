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
        Schema::create('delivery_signoffs', function (Blueprint $table) {
            $table->id();
            $table->string('express_sn')->index();
            $table->float('height')->nullable();
            $table->decimal('width', 8, 2)->nullable();
            $table->decimal('length', 8, 2)->nullable();
            $table->decimal('volume', 10, 5);
            $table->decimal('weight', 8, 2);
            $table->decimal('freight_price', 10, 2);
            $table->decimal('freight_cost', 10, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('delivery_signoffs');
    }
};
