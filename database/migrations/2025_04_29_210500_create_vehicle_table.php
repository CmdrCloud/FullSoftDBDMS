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
        Schema::create('vehicle', function (Blueprint $table) {
            $table->id();
            $table->string('model');
            $table->string('brand');
            $table->string('cylinders');
            $table->string('numberPlate')->nullable();
            $table->integer('year');
            $table->string('imgPath')->nullable();
            $table->boolean('airConditioning')->default(false);
            $table->boolean('metallicPaint')->default(false);
            $table->boolean('partOfPayment')->default(false);
            $table->decimal('price', 8, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicle');
    }
};
