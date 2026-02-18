<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class InitiativeStatus extends Model
{
    public const DRAFTING = 1;
    public const PROPOSE = 2;
    public const REVIEW = 3;
    public const APPROVE = 4;
    public const BASELINE = 5;

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
