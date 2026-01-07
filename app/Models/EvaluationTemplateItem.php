<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EvaluationTemplateItem extends Model
{
    use HasFactory;

    public const DOMAINS = [
        'tecnico' => 'Tecnico',
        'fisico' => 'Fisico',
        'tatico' => 'Tatico',
        'mental' => 'Mental',
    ];

    protected $fillable = [
        'template_id',
        'domain',
        'label',
        'weight',
        'sort_order',
    ];

    protected $casts = [
        'weight' => 'decimal:2',
    ];

    public function template()
    {
        return $this->belongsTo(EvaluationTemplate::class, 'template_id');
    }

    public function scores()
    {
        return $this->hasMany(EvaluationScore::class, 'template_item_id');
    }
}
