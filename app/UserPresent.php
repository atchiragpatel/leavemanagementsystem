<?php
/**
 * Created by PhpStorm.
 * User: chiragpatel
 * Date: 24/1/17
 * Time: 2:08 PM
 */

namespace App;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserPresent extends Model
{
    protected $table = 'user_present_log';
    use SoftDeletes;

    const STATUS = [
        "APPROVED",
        "REJECTED",
        "CONFLICT",
        "PENDING",
    ];

    protected $fillable = [
        'user_id',
        'action_type',
        'action_time',
        'status',
        'deleted_at',
    ];
}