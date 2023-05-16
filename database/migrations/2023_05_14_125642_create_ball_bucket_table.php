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
        Schema::create('ball_buckets', function (Blueprint $table) {
            $table->id();
            $table->integer('bucket_id');
            $table->integer('ball_id');
            $table->integer('no_of_ball');
            $table->float('volume', 8,2);
            $table->string('session_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ball_buckets');
    }
};
