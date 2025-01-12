<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Task extends Model
{
    protected $fillable = ['title', 'description', 'status', 'priority'];


    public function users():BelongsToMany
    {
        return $this->belongsToMany(User::class)->withPivot('is_finished')->withTimestamps();
    }
}
