<?php
declare(strict_types=1);

namespace App\Models\Reference;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RefCreature extends Model
{
    protected $table = 'ref_creatures';
    protected $primaryKey = 'ID';
    public $timestamps = false;
    protected $guarded = [];

    public function creatureType(): BelongsTo
    {
        return $this->belongsTo(RefCreatureType::class, 'Type', 'ID');
    }
}
