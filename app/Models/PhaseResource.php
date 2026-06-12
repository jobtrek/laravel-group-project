<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PhaseResource extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'resource_type', 'description', 'work_rate',
        'amount_needed', 'amount_found', 'phase_id',
    ];
}
