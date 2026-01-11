<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class OperationalPlan extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'planning_request_item_id',
        'created_by',
    ];

    /**
     * Get the planning request item that owns the plan.
     */
    public function planningRequestItem(): BelongsTo
    {
        return $this->belongsTo(PlanningRequestItem::class);
    }

    /**
     * Get the creator of the operational plan.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get all versions for the operational plan.
     */
    public function versions(): HasMany
    {
        return $this->hasMany(OperationalPlanVersion::class);
    }

    /**
     * Get the active version of the operational plan.
     */
    public function activeVersion(): HasOne
    {
        return $this->hasOne(OperationalPlanVersion::class)->where('is_active', true);
    }
}
