<?php

namespace App\Models;

use App\Models\Concerns\OrganizationScoped;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use HasFactory;
    use HasUlids;
    use OrganizationScoped;

    protected $fillable = [
        'organization_id',
        'evaluation_id',
        'status',
        'pdf_path',
        'public_token',
    ];

    protected $keyType = 'string';

    public $incrementing = false;

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function evaluation()
    {
        return $this->belongsTo(Evaluation::class);
    }
}
