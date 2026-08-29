<?php
declare(strict_types=1);

namespace App\Models\Reference;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RefSpell extends Model
{
    protected $table = 'ref_spells';
    protected $primaryKey = 'ID';
    public $timestamps = false;
    protected $guarded = [];

    public function options(): HasMany
    {
        return $this->hasMany(RefSpellOption::class, 'Spell', 'ID');
    }
}
