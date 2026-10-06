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
        Schema::table('tickets', function (Blueprint $table) {
            $table->string('prioridad')->default('media')->after('estado'); // baja, media, alta, urgente
            $table->foreignId('categoria_id')
                ->nullable()
                ->after('prioridad')
                ->constrained('categorias')
                ->nullOnDelete();
            $table->foreignId('asignado_a')
                ->nullable()
                ->after('categoria_id')
                ->constrained('users')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropForeign(['categoria_id']);
            $table->dropForeign(['asignado_a']);
            $table->dropColumn(['prioridad', 'categoria_id', 'asignado_a']);
        });
    }
};
