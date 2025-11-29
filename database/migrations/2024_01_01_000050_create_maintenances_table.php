<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('maintenances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_id')->constrained()->cascadeOnDelete();
            $table->foreignId('reported_by')->constrained('users')->cascadeOnDelete();
            $table->enum('type', ['maintenance', 'repair']);
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('status')->default('open');
            $table->decimal('cost', 10, 2)->nullable();
            $table->unsignedInteger('mileage')->nullable();
            $table->date('performed_at')->nullable();
            $table->date('next_due_at')->nullable();
            $table->foreignId('garage_id')->nullable()->constrained('users')->nullOnDelete();
            $table->json('attachments')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('maintenances');
    }
};
