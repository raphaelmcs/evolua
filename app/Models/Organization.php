<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Organization extends Model
{
    use HasFactory;
    use HasUlids;

    protected $fillable = [
        'name',
        'slug',
        'logo_path',
        'primary_color',
    ];

    protected $keyType = 'string';

    public $incrementing = false;

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function athletes()
    {
        return $this->hasMany(Athlete::class);
    }

    public function templates()
    {
        return $this->hasMany(EvaluationTemplate::class);
    }

    public function evaluations()
    {
        return $this->hasMany(Evaluation::class);
    }

    public function reports()
    {
        return $this->hasMany(Report::class);
    }

    public function subscription()
    {
        return $this->hasOne(Subscription::class);
    }
}
