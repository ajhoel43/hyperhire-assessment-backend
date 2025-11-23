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
        Schema::create('accounts', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email');
            $table->integer('age');
            $table->longText('pictures')->nullable();
            $table->string('location');
            $table->timestamps();
        });

        Schema::create("like_counters", function(Blueprint $table) {
            $table->foreignId('account_id')->references('id')->on('accounts')->onDelete('cascade');
            $table->integer('like_count')->default(0);
            $table->integer('dislike_count')->default(0);
            $table->primary('account_id');
            $table->timestamps();
        });

        Schema::create('like_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('account_id')->references('id')->on('accounts')->onDelete('cascade'); 
            $table->enum('action', ['like', 'dislike']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accounts');
        Schema::dropIfExists('like_counters');
        Schema::dropIfExists('like_logs');
    }
};
