<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocSession extends Model
{
    protected $guarded = [];

    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }
    public function clinic()
    {
        return $this->belongsTo(Clinic::class);
    }
}
