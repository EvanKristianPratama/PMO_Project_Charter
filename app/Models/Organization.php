<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Organization extends Model
{
    protected $table = 'trs_organization';

    protected $fillable = ['groub_id', 'name'];

    public function groub()
    {
        return $this->belongsTo(Groub::class, 'groub_id');
    }
}
