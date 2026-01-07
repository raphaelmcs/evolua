<?php

namespace App\Models;

use App\Models\Concerns\OrganizationScoped;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EvaluationTemplate extends Model
{
    use HasFactory;
    use HasUlids;
    use OrganizationScoped;

    protected bool $organizationScopeIncludeNull = true;

    protected $fillable = [
        'organization_id',
        'sport',
        'name',
        'scale_min',
        'scale_max',
        'is_default',
    ];

    protected $casts = [
        'scale_min' => 'decimal:2',
        'scale_max' => 'decimal:2',
        'is_default' => 'boolean',
    ];

    protected $keyType = 'string';

    public $incrementing = false;

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function items()
    {
        return $this->hasMany(EvaluationTemplateItem::class, 'template_id')
            ->orderBy('sort_order');
    }

    public function evaluations()
    {
        return $this->hasMany(Evaluation::class, 'template_id');
    }

    public function scopeDefaultForOrganization($query, string $organizationId)
    {
        return $query->where('is_default', true)
            ->where(function ($inner) use ($organizationId) {
                $inner->where('organization_id', $organizationId)
                    ->orWhereNull('organization_id');
            })
            ->orderByDesc('organization_id');
    }
}
