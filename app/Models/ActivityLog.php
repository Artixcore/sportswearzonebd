<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Request;

class ActivityLog extends Model
{
    protected $fillable = [
        'user_id',
        'subject_type',
        'subject_id',
        'action',
        'description',
        'old_values',
        'new_values',
        'ip',
    ];

    protected function casts(): array
    {
        return [
            'old_values' => 'array',
            'new_values' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function log(string $action, ?string $description = null, ?Model $subject = null, array $oldValues = [], array $newValues = []): self
    {
        return self::create([
            'user_id' => auth()->id(),
            'subject_type' => $subject ? get_class($subject) : null,
            'subject_id' => $subject?->getKey(),
            'action' => $action,
            'description' => $description ?? $action,
            'old_values' => $oldValues ?: null,
            'new_values' => $newValues ?: null,
            'ip' => Request::ip(),
        ]);
    }
}
