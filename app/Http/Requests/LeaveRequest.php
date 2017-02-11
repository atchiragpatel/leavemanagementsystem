<?php

namespace App\Http\Requests;

use App\LeaveTransaction;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Created by PhpStorm.
 * User: chiragpatel
 * Date: 8/2/17
 * Time: 11:08 AM
 */
class LeaveRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    public function messages()
    {
        return [
            'user_comments.is_sandwitch' => "You are in a Sandwitch",
            'user_comments.is_valid_leave' => "You are not eligible for this leave type",

        ];
    }
    public function rules()
    {
        $rules = [
            'leave_type' => 'required|in:'.implode(',',LeaveTransaction::LEAVE_TYPE),
            'from_date' => 'required',
            'to_date' => 'required',
            'user_comments' => 'required'
        ];
        return $rules;
    }
}