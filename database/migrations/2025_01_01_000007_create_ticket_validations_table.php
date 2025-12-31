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
        Schema::create('ticket_validations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transaction_id')->constrained('transactions')->onDelete('cascade');
            $table->dateTime('scanned_at');
            $table->foreignId('scanned_by')->constrained('users')->onDelete('cascade');
            $table->enum('status', ['valid', 'already_used'])->default('valid');
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->index('transaction_id');
            $table->index('scanned_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ticket_validations');
    }
};
