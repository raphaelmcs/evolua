<?php

namespace App\Models;

use App\Models\Concerns\OrganizationScoped;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Evaluation extends Model
{
    use HasFactory;
    use HasUlids;
    use OrganizationScoped;

    protected $fillable = [
        'organization_id',
        'athlete_id',
        'template_id',
        'evaluated_at',
        'evaluator_user_id',
        'notes',
        'visibility',
    ];

    protected $casts = [
        'evaluated_at' => 'datetime',
    ];

    protected $keyType = 'string';

    public $incrementing = false;

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function athlete()
    {
        return $this->belongsTo(Athlete::class);
    }

    public function template()
    {
        return $this->belongsTo(EvaluationTemplate::class, 'template_id');
    }

    public function scores()
    {
        return $this->hasMany(EvaluationScore::class);
    }

    public function evaluator()
    {
        return $this->belongsTo(User::class, 'evaluator_user_id');
    }

    public function report()
    {
        return $this->hasOne(Report::class);
    }

    public function domainAverages(): array
    {
        $scores = $this->scores->loadMissing('templateItem');
        $domains = array_keys(EvaluationTemplateItem::DOMAINS);
        $averages = [];

        foreach ($domains as $domain) {
            $domainScores = $scores->filter(function (EvaluationScore $score) use ($domain) {
                return $score->templateItem && $score->templateItem->domain === $domain;
            });

            $weightSum = $domainScores->sum(function (EvaluationScore $score) {
                return (float) $score->templateItem->weight;
            });

            $weightedSum = $domainScores->sum(function (EvaluationScore $score) {
                return (float) $score->score * (float) $score->templateItem->weight;
            });

            $averages[$domain] = $weightSum > 0 ? round($weightedSum / $weightSum, 2) : null;
        }

        return $averages;
    }
}
