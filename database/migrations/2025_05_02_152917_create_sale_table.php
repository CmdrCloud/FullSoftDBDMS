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
        Schema::create('sale', function (Blueprint $table) {
            $table->id();
            $table->foreignId('IDVehicle')->constrained('vehicle')->onDelete('cascade');
            $table->foreignId('IDClient')->constrained('client')->onDelete('cascade');
            $table->foreignId('IDUser')->constrained('users')->onDelete('cascade');
            $table->datetime('date');
            $table->float('totalAmount');
            $table->float('totalUpfront');
            $table->float('totalPartPayment');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sale');
    }
};
