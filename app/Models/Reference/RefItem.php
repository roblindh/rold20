<?php
declare(strict_types=1);

namespace App\Models\Reference;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RefItem extends Model
{
    protected $table = 'ref_items';
    protected $primaryKey = 'ID';
    public $timestamps = false;
    protected $guarded = [];

    public function itemType(): BelongsTo
    {
        return $this->belongsTo(RefItemType::class, 'Type', 'ID');
    }

    public function itemSubtype(): BelongsTo
    {
        return $this->belongsTo(RefItemSubtype::class, 'Subtype', 'ID');
    }
}
