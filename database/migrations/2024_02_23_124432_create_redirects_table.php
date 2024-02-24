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
        Schema::create('redirects', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();;
            $table->string('url_destino');
            $table->boolean('ativo')->default(true);
            $table->dateTime('ultimo_acesso')->nullable();
            $table->timestamps(); // Adiciona automaticamente as colunas "created_at" e "updated_at"
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('redirects');
    }
};
