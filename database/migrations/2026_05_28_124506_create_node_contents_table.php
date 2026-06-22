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
        Schema::create('node_contents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('roadmap_id')->constrained()->cascadeOnDelete();
            $table->string('node_id'); // Строковый ID из vue-flow (например, 'node_1')
            $table->text('content')->nullable();
            
            // jsonb для хранения массива ссылок
            $table->jsonb('links')->nullable(); 
            $table->string('image_path')->nullable();
            
            // Защита от дублей контента для одного и того же узла
            $table->unique(['roadmap_id', 'node_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('node_contents');
    }
};
