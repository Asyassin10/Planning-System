<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExecutionEvent extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'operational_plan_version_id',
        'event_type',
        'event_data',
        'recorded_by',
        'recorded_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'event_data' => 'array',
            'recorded_at' => 'datetime',
        ];
    }

    /**
     * Get the operational plan version that owns the event.
     */
    public function operationalPlanVersion(): BelongsTo
    {
        return $this->belongsTo(OperationalPlanVersion::class);
    }

    /**
     * Get the user who recorded the event.
     */
    public function recorder(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }
}
