<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('structure_id')->nullable()->constrained()->nullOnDelete();
            $table->string('registration')->unique();
            $table->string('brand');
            $table->string('model');
            $table->year('year')->nullable();
            $table->string('vin')->nullable();
            $table->enum('status', ['active', 'archived'])->default('active');
            $table->unsignedInteger('mileage')->default(0);
            $table->string('fuel_type')->nullable();
            $table->date('purchase_date')->nullable();
            $table->timestamp('archived_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};
