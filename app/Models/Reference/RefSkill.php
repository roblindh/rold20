<?php
declare(strict_types=1);

namespace App\Models\Reference;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RefSkill extends Model
{
    protected $table = 'ref_skills';
    protected $primaryKey = 'ID';
    public $timestamps = false;
    protected $guarded = [];

    public function skillType(): BelongsTo
    {
        return $this->belongsTo(RefSkillType::class, 'Type', 'ID');
    }

    public function benefits(): HasMany
    {
        return $this->hasMany(RefSkillBenefit::class, 'SkillID', 'ID');
    }

    public function specializations(): HasMany
    {
        return $this->hasMany(RefSkillSpecialization::class, 'Skill', 'ID');
    }

    public function access(): HasMany
    {
        return $this->hasMany(RefSkillAccess::class, 'SkillID', 'ID');
    }
}
