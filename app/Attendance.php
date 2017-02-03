<?php
/**
 * Created by PhpStorm.
 * User: chiragpatel
 * Date: 2/2/17
 * Time: 11:05 AM
 */

namespace App;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Attendance extends Model
{
    protected $table = 'attendance';
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