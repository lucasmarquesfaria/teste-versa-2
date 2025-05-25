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
        Schema::create('baixas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('distribuicao_id')->constrained('distribuicoes')->onDelete('cascade');
            $table->unsignedInteger('numero');
            $table->date('data_devolucao');
            $table->enum('situacao', ['utilizada', 'cancelada', 'nao_utilizada']);
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
        Schema::dropIfExists('baixas');
    }
};
