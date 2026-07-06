<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrsDefinition extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'mst_definition';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'definition',
    ];
}
