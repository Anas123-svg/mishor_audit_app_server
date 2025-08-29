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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('client_id'); // Match with 'clients' table's 'id'
            $table->string('phone');
            $table->string('name');
            $table->string('email')->unique();
            $table->string('role')->nullable();
            $table->string('profile_image')->nullable();
            $table->string('password');
            $table->boolean('is_verified')->default(false);
            $table->timestamps();
            $table->foreign('client_id')->references('id')->on('clients')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
