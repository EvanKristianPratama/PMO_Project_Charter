<?php

namespace Modules\ITOM\Models;

use Illuminate\Database\Eloquent\Model;

class MstBusinessCapability extends Model
{
    protected $table = 'mst_business_capability';

    protected $fillable = [
        'group_business',
        'group_function',
        'subGroup_function',
        'subSubGroup_function',
    ];
}
