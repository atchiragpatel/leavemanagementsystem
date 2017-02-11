@extends('layouts.app')
@section('content')
    <div class="container">
        <table class="table">
            <thead>
            <tr>
                <th>Name</th>
                <th>From Date</th>
                <th>To Date</th>
                <th>Total Days</th>
                <th>Reason for Leave</th>
                <th>&nbsp;</th>
            </tr>
            </thead>
            <tbody>
            @foreach($leaverequest as $request)
                <?php
                $userId = $request->user_id;
                $userFirstName = \App\User::find($userId)->fname;
                $userLastName = \App\User::find($userId)->lname;
                ?>
                <tr>
                    <td><?php echo $userFirstName . ' ' . $userLastName ?></td>
                    <td><?php echo $request['from_date'] ?></td>
                    <td><?php echo $request['to_date'] ?></td>
                    <td><?php echo $request['value'] ?></td>
                    <td><?php echo $request['user_comments'] ?></td>
                    <td>
                    <a href="{{ action('LeaveTransactionController@leaveApprove',$request['id']) }}" class="btn btn-primary">Approve</a>
                    <a href="{{ action('LeaveTransactionController@leaveReject',$request['id']) }}" class="btn btn-danger">Reject</a>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endsection