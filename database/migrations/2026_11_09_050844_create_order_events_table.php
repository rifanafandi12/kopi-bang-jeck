<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->string('event', 40);
            $table->dateTime('occurred_at', 3);
            $table->string('actor', 20);
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->index(['order_id', 'event']);
            $table->index('occurred_at');
            $table->index('event');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_events');
    }
};
