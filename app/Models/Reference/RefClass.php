<?php
declare(strict_types=1);

namespace App\Models\Reference;

use Illuminate\Database\Eloquent\Model;

class RefClass extends Model
{
    protected $table = 'ref_classes';
    protected $primaryKey = 'ID';
    public $timestamps = false;
    protected $guarded = [];
}
