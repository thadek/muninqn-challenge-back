<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;


class Task extends Model
{
    /** @use HasFactory<\Database\Factories\TaskFactory> */
    use HasFactory;
    protected $fillable = ['title', 'description', 'status', 'priority'];

    public $timestamps = true;
    public const STATUSES = ['pendiente', 'en progreso', 'completada'];
    public const PRIORITIES = ['baja', 'media', 'alta'];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)->withPivot('is_finished')->withTimestamps();
    }
}
