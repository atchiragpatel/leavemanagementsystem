@extends('layouts.app')
@section('content')
    <div class="container">
        <div class="panel-body">
            Add Bank Detail <a href="addbankdetails" class="btn btn-primary pull-right mb-2 mt-2">Add
                Bank Detail</a>
        </div>
        <table class="table">
            <thead>
            <tr>
                <th>Bank Name</th>
                <th>Branch Name</th>
                <th>Account Number</th>
                <th>IFSC Code</th>
                <th>&nbsp;</th>
            </tr>
            </thead>
            <tbody>
            @foreach($bankdetails as $bank)
                <tr>
                    <td><?php echo $bank['bank_name'] ?></td>
                    <td><?php echo $bank['branch_name'] ?></td>
                    <td><?php echo $bank['account_number'] ?></td>
                    <td><?php echo $bank['ifsc_code'] ?></td>
                    <td>
                        <a href="{{ action('UserController@updateBankDetails',$bank['id'])  }}" class="btn btn-primary">Edit</a>
                        <a href="{{ action('UserController@deleteBankDetails',$bank['id'])  }}" class="btn btn-danger">Delete</a>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endsection