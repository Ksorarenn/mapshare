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
        Schema::create('user_roadmap_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('roadmap_id')->constrained()->cascadeOnDelete();
            
            // Массив пройденных нод
            $table->jsonb('completed_nodes')->default('[]'); 
            
            // Один пользователь имеет только одну запись прогресса для конкретной карты
            $table->unique(['user_id', 'roadmap_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_roadmap_progress');
    }
};
