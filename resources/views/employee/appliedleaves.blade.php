@extends('layouts.app')
@section('content')
    <div class="container">
        <table class="table">
            <thead>
            <tr>
                <th>From Date</th>
                <th>To Date</th>
                <th>Total Days</th>
                <th>Status</th>
                <th>User Comment</th>
                <th>Manager Comment</th>
                <th>&nbsp;</th>
            </tr>
            </thead>
            <tbody>
            @foreach($leaveapplied as $leavedate)
                <tr>
                    <td><?php echo $leavedate['from_date'] ?></td>
                    <td><?php echo $leavedate['to_date'] ?></td>
                    <td><?php echo $leavedate['value'] ?></td>
                    <td><?php echo $leavedate['status'] ?></td>
                    <td><?php echo $leavedate['user_comments'] ?></td>
                    <td><?php echo $leavedate['manager_comments'] ?></td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endsection