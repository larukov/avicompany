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
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->string('flight_number');
            $table->string('airline');
            $table->string('airline_logo')->nullable();
            $table->string('aircraft');
            
            // Маршрут
            $table->string('departure_city');
            $table->string('departure_airport');
            $table->string('departure_airport_name');
            $table->dateTime('departure_time');
            
            $table->string('arrival_city');
            $table->string('arrival_airport');
            $table->string('arrival_airport_name');
            $table->dateTime('arrival_time');
            
            // Информация о рейсе
            $table->integer('duration_minutes'); // 90
            $table->boolean('is_direct')->default(true);
            $table->integer('stops')->default(0);
            
            // Классы обслуживания и цены
            $table->decimal('price_economy', 10, 2);
            $table->decimal('price_comfort', 10, 2)->nullable();
            $table->decimal('price_business', 10, 2)->nullable();
            
            // Вместимость
            $table->integer('total_seats');
            $table->integer('available_seats_economy');
            $table->integer('available_seats_comfort')->nullable();
            $table->integer('available_seats_business')->nullable();
            
            // Удобства
            $table->boolean('has_wifi')->default(false);
            $table->boolean('has_meal')->default(false);
            $table->boolean('has_entertainment')->default(false);
            $table->integer('baggage_allowance')->default(23); // кг
            
            // Статус
            $table->enum('status', ['scheduled', 'boarding', 'departed', 'arrived', 'delayed', 'cancelled'])->default('scheduled');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
