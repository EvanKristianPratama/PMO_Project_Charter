<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class InitiativeStatus extends Model
{
    public const PROPOSE = 1;
    public const REVIEW = 2;
    public const APPROVE = 3;
    public const BASELINE = 4;

    protected $table = 'trs_status_initiative';

    public $incrementing = false;

    protected $keyType = 'int';

    protected $fillable = [
        'id',
        'name',
    ];

    protected function casts(): array
    {
        return [
            'id' => 'integer',
        ];
    }

    public static function ordered(): Collection
    {
        return static::query()
            ->orderBy('id')
            ->get(['id', 'name']);
    }

    public static function baselineId(): int
    {
        return (int) (static::query()
            ->where('name', 'baseline')
            ->value('id') ?? self::BASELINE);
    }
}
