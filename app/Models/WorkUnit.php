<?php

namespace App\Models;

use Database\Factories\WorkUnitFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'code', 'campus', 'type'])]
class WorkUnit extends Model
{
    /** @use HasFactory<WorkUnitFactory> */
    use HasFactory;

    /** @return HasMany<User> */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
}
