<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlanVersionResource extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'operational_plan_version_id',
        'resource_id',
        'capacity',
        'is_permanent',
        'notes',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_permanent' => 'boolean',
        ];
    }

    /**
     * Get the operational plan version that owns the resource.
     */
    public function operationalPlanVersion(): BelongsTo
    {
        return $this->belongsTo(OperationalPlanVersion::class);
    }

    /**
     * Get the resource.
     */
    public function resource(): BelongsTo
    {
        return $this->belongsTo(Resource::class);
    }
}
