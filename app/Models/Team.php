<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Team extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = ['name', 'slug', 'description', 'status'];

    protected static function booted(): void
    {
        static::creating(function (Team $team): void {
            $team->slug ??= str($team->name)->slug();
        });
    }

    public function members(): HasMany
    {
        return $this->hasMany(TeamMember::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(TeamMessage::class);
    }

    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }
}