<?php

namespace App\Http\Controllers;

use App\Bank;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use League\Flysystem\Exception;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserController extends Controller
{
    public function userInfo()
    {
        return view('user.userinfo');
    }

    public function updateUserInfo(Request $request)
    {
        $id = Auth::user()->id;
        $users = User::findOrFail($id);
        $users->blood_group = $request->blood_group;
        $users->dob = $request->dob;
        $users->contact_number = $request->contact_number;
        $users->city = $request->city;
        $users->state = $request->state;
        $users->country = $request->country;
        $users->zipcode = $request->zipcode;
        $users->address = $request->address;
        $users->emergency_contact_name = $request->emergency_contact_name;
        $users->emergency_contact_number = $request->emergency_contact_number;
        $users->emergency_contact_relation = $request->emergency_contact_relation;

        $users->save();

        return redirect('userinfo');
    }

    public function changeUserPassword()
    {
        return view('user.changeuserpassword');
    }

    public function updateUserPassword(Request $request)
    {

        $input = $request->all();
        $users = User::find(auth()->user()->id);

        if (!Hash::check($input['oldpassword'], $users->password)) {
            echo "Your old password does not match";
        } else {
            if ($request->newpassword == $request->confirmpassword) {
                $users->password = bcrypt($request->newpassword);

                $users->save();

                return redirect('userinfo');
            } else {
                echo "Your new password does not match with confirm password";
            }

        }
    }

    public function userBankDetails()
    {
        $bankDetails = Bank::where(['user_id' => Auth::user()->id])->get();
        return view('user.userbankdetail')->with('bankdetails', $bankDetails);
    }

    public function addBankDetails()
    {
        return view('user.addbankdetails');

    }

    public function storeBankDetails(Request $request)
    {
        $user_id = Auth::user()->id;
        $bankDetails = new Bank;

        $bankDetails->user_id = $user_id;
        $bankDetails->bank_name = $request->bank_name;
        $bankDetails->branch_name = $request->branch_name;
        $bankDetails->account_number = $request->account_number;
        $bankDetails->ifsc_code = $request->ifsc_code;

        $bankDetails->save();

        return redirect('userbankdetail');
    }

    public function updateBankDetails($id)
    {
        $bankData = Bank::find($id);
        return view('user.updatebankdetails')->with('bankdata',$bankData);
    }

    public function storeUpdatedBankDetails($id, Request $request)
    {
        $bankData = Bank::find($id);

        $bankData->bank_name = $request->bank_name;
        $bankData->branch_name = $request->branch_name;
        $bankData->account_number = $request->account_number;
        $bankData->ifsc_code = $request->ifsc_code;

        $bankData->save();

        return redirect('userbankdetail');
    }

    public function deleteBankDetails($id)
    {
        $bankData = Bank::find($id);
        $bankData->delete();

        return redirect('userbankdetail');
    }
}
