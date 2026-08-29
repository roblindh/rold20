<?php
declare(strict_types=1);

namespace App\Models\Dynamic;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Reference\RefClass;
use App\Models\Reference\RefCreature;

class Character extends Model
{
    protected $table = 'characters';
    protected $primaryKey = 'ID';
    public $timestamps = false;
    protected $guarded = [];

    public function player(): BelongsTo
    {
        return $this->belongsTo(Player::class, 'Player', 'ID');
    }

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class, 'Campaign', 'ID');
    }

    public function primaryClass(): BelongsTo
    {
        return $this->belongsTo(RefClass::class, 'ClassID', 'ID');
    }

    public function race(): BelongsTo
    {
        return $this->belongsTo(RefCreature::class, 'RaceID', 'ID');
    }
}
