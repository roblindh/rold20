<?php
declare(strict_types=1);

namespace App\Models\Dynamic;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Campaign extends Model
{
    protected $table = 'campaigns';
    protected $primaryKey = 'ID';
    public $timestamps = false;
    protected $guarded = [];

    public function characters(): HasMany
    {
        return $this->hasMany(Character::class, 'Campaign', 'ID');
    }
}
