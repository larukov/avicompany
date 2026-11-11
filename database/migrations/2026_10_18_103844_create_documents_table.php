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
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['passport_rf', 'passport_international', 'driver_license', 'other'])->default('passport_rf');
            $table->string('series')->nullable(); // Серия: 1234
            $table->string('number'); // Номер: 567890
            $table->string('issued_by')->nullable(); // Кем выдан
            $table->date('issued_at')->nullable(); // Дата выдачи
            $table->date('expires_at')->nullable(); // Действителен до
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
