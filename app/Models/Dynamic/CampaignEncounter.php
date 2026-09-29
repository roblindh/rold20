<?php
declare(strict_types=1);

namespace App\Models\Dynamic;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CampaignEncounter extends Model
{
    protected $table = 'campaign_encounters';
    protected $guarded = [];

    protected $casts = [
        'encounter_level' => 'float',
        'xp_award' => 'integer',
        'order_index' => 'integer',
        'monsters_and_npcs' => 'array',
        'traps_and_hazards' => 'array',
        'treasure_rewards' => 'array',
    ];

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class, 'campaign_id', 'ID');
    }

    public function adventure(): BelongsTo
    {
        return $this->belongsTo(CampaignAdventure::class, 'adventure_id', 'id');
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(CampaignLocation::class, 'location_id', 'id');
    }
}
