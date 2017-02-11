<?php
/**
 * Created by PhpStorm.
 * User: chiragpatel
 * Date: 24/1/17
 * Time: 4:17 PM
 */

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LeaveTransaction extends Model
{
    protected $table = 'leave_transaction';

    const LEAVE_TYPE = ["SICK LEAVE","CASUAL LEAVE","PRIVILEGE LEAVE","LEAVE WITHOUT PAY"];
    const TYPE = ["CREDIT","DEBIT"];

    protected $fillable = [
        'user_id','leave_type','value','type','status','ledger'];
}