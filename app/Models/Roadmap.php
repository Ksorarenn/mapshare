<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Roadmap extends Model
{
    // Mass assignable attributes, включая поля из ТЗ
    protected $fillable = [
        'user_id',
        'title',
        'description',
        'graph_data',
    ];

    // Приведение типов, graph_data → array
    protected $casts = [
        'graph_data' => 'array',
    ];

    // Скоуп для получения карт пользователя
    public function scopeOwnedBy($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    // Связи
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function nodeContents(): HasMany
    {
        return $this->hasMany(NodeContent::class);
    }

    public function progresses(): HasMany
    {
        return $this->hasMany(UserRoadmapProgress::class);
    }
}
