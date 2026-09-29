<?php
declare(strict_types=1);

namespace App\Models\Dynamic;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CampaignLocation extends Model
{
    protected $table = 'campaign_locations';
    protected $guarded = [];

    protected $casts = [
        'parent_location_id' => 'integer',
        'notable_npcs' => 'array',
        'inventory_and_services' => 'array',
        'rumors_and_hooks' => 'array',
    ];

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class, 'campaign_id', 'ID');
    }

    public function parentLocation(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_location_id', 'id');
    }

    public function childLocations(): HasMany
    {
        return $this->hasMany(self::class, 'parent_location_id', 'id')->orderBy('name');
    }

    public function encounters(): HasMany
    {
        return $this->hasMany(CampaignEncounter::class, 'location_id', 'id');
    }
}
