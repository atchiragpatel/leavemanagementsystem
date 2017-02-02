<?php
/**
 * Created by PhpStorm.
 * User: chiragpatel
 * Date: 20/1/17
 * Time: 12:45 PM
 */

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Holiday extends Model
{
    protected $table = 'holidays';
    use SoftDeletes;

    protected $fillable = [
        'festivalname','festivaldate','deleted_at'
    ];
}