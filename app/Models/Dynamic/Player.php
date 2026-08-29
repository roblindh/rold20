<?php
declare(strict_types=1);

namespace App\Models\Dynamic;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Auth\Authenticatable;

class Player extends Model implements AuthenticatableContract
{
    use Authenticatable;

    public const TYPE_PLAYER = 1;
    public const TYPE_GM = 2;

    protected $table = 'players';
    protected $primaryKey = 'ID';
    public $timestamps = false;
    protected $guarded = [];

    protected $hidden = [
        'Password',
    ];

    /**
     * Get the password for the user.
     */
    public function getAuthPassword(): ?string
    {
        return $this->Password;
    }

    /**
     * Check if the authenticated user is a Game Master (GM).
     */
    public function isGM(): bool
    {
        return (int)$this->Type === self::TYPE_GM;
    }

    /**
     * Check if the user is a Player.
     */
    public function isPlayer(): bool
    {
        return (int)$this->Type === self::TYPE_PLAYER || empty($this->Type);
    }

    /**
     * Human readable role name.
     */
    public function getRoleName(): string
    {
        return $this->isGM() ? 'Game Master' : 'Player';
    }

    /**
     * Characters created by or assigned to this player.
     */
    public function characters(): HasMany
    {
        return $this->hasMany(Character::class, 'Player', 'ID');
    }

    /**
     * Campaigns managed by this GM.
     */
    public function campaigns(): HasMany
    {
        return $this->hasMany(Campaign::class, 'GameMaster', 'ID');
    }
}
