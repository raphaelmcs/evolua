<?php

namespace App\Models;

use App\Models\Concerns\OrganizationScoped;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    use HasFactory;
    use OrganizationScoped;

    protected $fillable = [
        'organization_id',
        'plan',
        'status',
        'trial_ends_at',
        'current_period_ends_at',
        'max_active_athletes',
    ];

    protected $casts = [
        'trial_ends_at' => 'datetime',
        'current_period_ends_at' => 'datetime',
        'max_active_athletes' => 'integer',
    ];

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }
}
