<?php
/**
 * Created by PhpStorm.
 * User: chiragpatel
 * Date: 24/1/17
 * Time: 12:26 PM
 */

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Documents extends Model
{
    protected $table = 'documents';
    use SoftDeletes;

    protected $fillable = [
        'user_id','document_name','document_path','extension','deleted_at'
    ];
}