<?php
declare(strict_types=1);

namespace App\Models\Reference;

use Illuminate\Database\Eloquent\Model;

class RefCulture extends Model
{
    protected $table = 'ref_cultures';
    protected $primaryKey = 'ID';
    public $timestamps = false;
    protected $guarded = [];
}
