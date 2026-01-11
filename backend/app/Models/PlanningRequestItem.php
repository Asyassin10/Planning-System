<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class PlanningRequestItem extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'planning_request_id',
        'route_id',
        'capacity',
        'start_date',
        'end_date',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
        ];
    }

    /**
     * Get the planning request that owns the item.
     */
    public function planningRequest(): BelongsTo
    {
        return $this->belongsTo(PlanningRequest::class);
    }

    /**
     * Get the route for this item.
     */
    public function route(): BelongsTo
    {
        return $this->belongsTo(Route::class);
    }

    /**
     * Get the operational plan for this item.
     */
    public function operationalPlan(): HasOne
    {
        return $this->hasOne(OperationalPlan::class);
    }
}
