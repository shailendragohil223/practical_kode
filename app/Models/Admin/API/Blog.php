<?php

namespace App\Models\Admin\API;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Yajra\Auditable\AuditableWithDeletesTrait;

class Blog extends Model
{
    use HasFactory;
    use SoftDeletes;
    use AuditableWithDeletesTrait;
    public $table = "blog";

    protected $fillable = [
        'id',
        'title',
        'description',
        'image',
        'status',
    ];
}
