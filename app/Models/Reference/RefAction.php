<?php
declare(strict_types=1);

namespace App\Models\Reference;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RefAction extends Model
{
    protected $table = 'ref_actions';
    protected $primaryKey = 'ID';
    public $timestamps = false;
    protected $guarded = [];

    public function actionType(): BelongsTo
    {
        return $this->belongsTo(RefActionType::class, 'Category', 'ID');
    }
}
