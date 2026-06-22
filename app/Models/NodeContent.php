<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NodeContent extends Model
{
    // Отключаем таймстампы (created_at/updated_at), так как в миграции мы их не создавали
    public $timestamps = false;

    protected $fillable = ['roadmap_id', 'node_id', 'content', 'links', 'image_path'];

    // Кастуем ссылки из JSONB в массив
    protected $casts = [
        'links' => 'array',
    ];

    public function roadmap(): BelongsTo
    {
        return $this->belongsTo(Roadmap::class);
    }
}
