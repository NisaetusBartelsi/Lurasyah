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
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->string('CompanyName');
            $table->enum('CompanyType', ['Kafe','Tempat Wisata','MiniMarket'])->nullable()->Collation('utf8_general_Ci');
            $table->text('CompanyAddres');
            $table->string('CompanyProvince');
            $table->string('CompanyRegency');
            $table->string('CompanyDistrict');
            $table->string('CompanyVillage');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
