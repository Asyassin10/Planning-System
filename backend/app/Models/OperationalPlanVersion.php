<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class OperationalPlanVersion extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'operational_plan_id',
        'version',
        'is_active',
        'valid_from',
        'valid_to',
        'notes',
        'created_by',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'valid_from' => 'date',
            'valid_to' => 'date',
        ];
    }

    /**
     * Get the operational plan that owns the version.
     */
    public function operationalPlan(): BelongsTo
    {
        return $this->belongsTo(OperationalPlan::class);
    }

    /**
     * Get the creator of the version.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the resources for this version.
     */
    public function resources(): HasMany
    {
        return $this->hasMany(PlanVersionResource::class);
    }

    /**
     * Get the execution events for this version.
     */
    public function executionEvents(): HasMany
    {
        return $this->hasMany(ExecutionEvent::class);
    }

    /**
     * Activate this version and deactivate all other versions of the same plan.
     */
    public function activate(): bool
    {
        return DB::transaction(function () {
            // Deactivate all other versions of this operational plan
            OperationalPlanVersion::where('operational_plan_id', $this->operational_plan_id)
                ->where('id', '!=', $this->id)
                ->update(['is_active' => false]);

            // Activate this version
            $this->is_active = true;

            return $this->save();
        });
    }
}
