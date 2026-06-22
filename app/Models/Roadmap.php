<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Roadmap extends Model
{
    // Разрешаем массовое заполнение полей
    protected $fillable = ['user_id', 'title', 'description', 'graph_data'];

    // КРИТИЧЕСКИ ВАЖНО: автоматически превращает JSONB из базы в PHP-массив
    protected $casts = [
        'graph_data' => 'array',
    ];

    // Связь: Роадмап принадлежит пользователю
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Связь: У роадмапа много контента для нод
    public function nodeContents(): HasMany
    {
        return $this->hasMany(NodeContent::class);
    }
}
