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
            $table->string('name');
            $table->string('password');
            $table->string('email')->unique();
            $table->string('role')->default('user');
            $table->string('images')->default('https://bit.ly/3pETYdu');
            $table->string('otp_code')->nullable();
            $table->boolean('triger')->nullable();
            $table->string('biodata')->nullable();
            $table->timestamp('otp_expired')->nullable();
            $table->string('provinsi')->default('Indonesia');
            $table->string('kota')->default('Indonesia');
            $table->string('kecamatan')->default('Indonesia');
            $table->string('desa')->default('Indonesia');
            $table->timestamp('email_verified_at')->nullable();
            $table->rememberToken();
            $table->timestamps();
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
