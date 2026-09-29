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

    public function adventures(): HasMany
    {
        return $this->hasMany(CampaignAdventure::class, 'campaign_id', 'ID')->orderBy('order_index')->orderBy('id');
    }

    public function encounters(): HasMany
    {
        return $this->hasMany(CampaignEncounter::class, 'campaign_id', 'ID')->orderBy('order_index')->orderBy('id');
    }

    public function locations(): HasMany
    {
        return $this->hasMany(CampaignLocation::class, 'campaign_id', 'ID')->orderBy('name');
    }

    public function gameMaster(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Player::class, 'GameMaster', 'ID');
    }
}
