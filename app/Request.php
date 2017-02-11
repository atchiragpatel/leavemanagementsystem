<?php
/**
 * Created by PhpStorm.
 * User: chiragpatel
 * Date: 3/2/17
 * Time: 5:57 PM
 */

namespace App;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Request extends Model
{
    protected $table = 'request';
    use SoftDeletes;

    const STATUS = ["REQUESTED","APPROVED","REJECTED","PROCESSED"];
    const TYPE = ["CREDIT","DEBIT"];

    protected $fillable = ['user_id','type','value','txn_type','from_date','to_date','status','user_comments','manager_comments','deleted_at'];
}