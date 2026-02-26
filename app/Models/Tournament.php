<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Tournament extends Model
{
    use HasFactory;

    protected $fillable = [
        'name','slug','sport','season','location','start_date','end_date','format',
        'points_win','points_draw','points_loss','tiebreakers','status',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'tiebreakers' => 'array',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $tournament): void {
            if (! $tournament->slug) {
                $tournament->slug = Str::slug($tournament->name.'-'.$tournament->season);
            }
        });
    }

    public function teams(): BelongsToMany
    {
        return $this->belongsToMany(Team::class)->withPivot(['seed', 'group_id'])->withTimestamps();
    }

    public function groups(): HasMany
    {
        return $this->hasMany(Group::class);
    }

    public function matches(): HasMany
    {
        return $this->hasMany(TournamentMatch::class);
    }
}
