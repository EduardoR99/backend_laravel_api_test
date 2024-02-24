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
        Schema::create('redirect_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('redirect_id');
            $table->string('redirect_id_code');
            $table->string('ip');
            $table->string('user_agent');
            $table->string('header_referer')->nullable();
            $table->json('query_params')->nullable();
            $table->timestamps();

            $table->foreign('redirect_id')->references('id')->on('redirects')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('redirect_logs');
    }
};
