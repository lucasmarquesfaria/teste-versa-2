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
        Schema::create('distribuicoes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('instituicao_id')->constrained('instituicoes')->onDelete('cascade');
            $table->enum('tipo_certidao', ['obito', 'nascidos_vivos']);
            $table->unsignedInteger('numero_inicial');
            $table->unsignedInteger('numero_final');
            $table->date('data_entrega');
            $table->text('observacao')->nullable();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('distribuicoes');
    }
};
