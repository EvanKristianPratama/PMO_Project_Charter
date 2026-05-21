<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BpmnWorkflow extends Model
{
    protected $table = 'bpmn_workflows';

    protected $fillable = [
        'name',
        'description',
        'flow_data',
        'bpmn_xml',
        'is_active',
    ];

    protected $casts = [
        'flow_data' => 'array',
        'is_active' => 'boolean',
    ];
}
