<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;

class MstSkOrganization extends Model
{
    use LogsActivity;

    protected $table = 'mst_sk_organization';

    protected $fillable = [
        'sk',
        'deskripsi',
    ];
    
}
