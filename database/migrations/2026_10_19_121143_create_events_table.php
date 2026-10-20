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
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->integer('organization_id')->unsigned();
            $table->integer('sports_id')->unsigned();
            $table->integer('created_by')->unsigned();
            $table->string('title');
            $table->string('description');
            $table->dateTime('starts_at');
            $table->double('duration_minutes');
            $table->string('city');
            $table->string('venue');
            $table->integer('capacity');
            $table->integer('public_token')->unique();
            $table->string('status');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
