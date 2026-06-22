<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserRoadmapProgress extends Model
{
    public $timestamps = false;
    
    // Переопределяем имя таблицы, так как Laravel по умолчанию будет искать во множественном числе (user_roadmap_progresses)
    protected $table = 'user_roadmap_progress';

    protected $fillable = ['user_id', 'roadmap_id', 'completed_nodes'];

    // Кастуем список пройденных нод в массив (например: ['node_1', 'node_2'])
    protected $casts = [
        'completed_nodes' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function roadmap(): BelongsTo
    {
        return $this->belongsTo(Roadmap::class);
    }
}
