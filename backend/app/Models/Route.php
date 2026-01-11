<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Route extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'identifier',
        'description',
    ];

    /**
     * Get the planning request items for this route.
     */
    public function planningRequestItems(): HasMany
    {
        return $this->hasMany(PlanningRequestItem::class);
    }
}
