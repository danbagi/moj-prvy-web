<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('tournaments', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->enum('sport', ['FOOTBALL', 'BASKETBALL', 'HANDBALL', 'VOLLEYBALL', 'OTHER']);
            $table->string('season', 20);
            $table->string('location')->nullable();
            $table->dateTime('start_date')->nullable();
            $table->dateTime('end_date')->nullable();
            $table->enum('format', ['GROUPS_ONLY', 'GROUPS_PLUS_PLAYOFF', 'ROUND_ROBIN', 'KNOCKOUT']);
            $table->unsignedSmallInteger('points_win')->default(3);
            $table->unsignedSmallInteger('points_draw')->default(1);
            $table->unsignedSmallInteger('points_loss')->default(0);
            $table->json('tiebreakers')->nullable();
            $table->enum('status', ['DRAFT', 'PUBLISHED', 'FINISHED'])->default('DRAFT');
            $table->timestamps();
        });
    }

    public function down(): void { Schema::dropIfExists('tournaments'); }
};
