<?php
declare(strict_types=1);

namespace App\Models\Dynamic;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CampaignAdventure extends Model
{
    protected $table = 'campaign_adventures';
    protected $guarded = [];

    protected $casts = [
        'min_level' => 'integer',
        'max_level' => 'integer',
        'order_index' => 'integer',
    ];

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class, 'campaign_id', 'ID');
    }

    public function encounters(): HasMany
    {
        return $this->hasMany(CampaignEncounter::class, 'adventure_id', 'id')->orderBy('order_index')->orderBy('id');
    }
}
