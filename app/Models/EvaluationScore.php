<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EvaluationScore extends Model
{
    use HasFactory;

    protected $fillable = [
        'evaluation_id',
        'template_item_id',
        'score',
        'comment',
    ];

    protected $casts = [
        'score' => 'decimal:2',
    ];

    public function evaluation()
    {
        return $this->belongsTo(Evaluation::class);
    }

    public function templateItem()
    {
        return $this->belongsTo(EvaluationTemplateItem::class, 'template_item_id');
    }
}
