<?php
/**
 * Created by PhpStorm.
 * User: chiragpatel
 * Date: 23/1/17
 * Time: 3:01 PM
 */

namespace App;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Bank extends Model
{
    protected $table = 'bank';
    use SoftDeletes;

    protected $fillable = [
        'user_id','bank_name','branch_name','account_number','ifsc_code','deleted_at'
    ];

}