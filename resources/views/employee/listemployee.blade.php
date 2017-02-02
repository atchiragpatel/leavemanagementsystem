@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="panel-body">
            Create Employee <a href="addemployee" class="btn btn-primary pull-right mb-2 mt-2">Add
                Employee</a>
        </div>
        <table class="table">
            <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Joining Date</th>
                <th>&nbsp;</th>
            </tr>
            </thead>
            <tbody>
            @foreach($users as $user)
                <tr>
                    <td><?php echo $user['fname'] . ' ' . $user['lname'] ?></td>
                    <td><?php echo $user['email'] ?></td>
                    <td><?php echo $user['doj'] ?></td>
                    <td>
                        <a href="{{ action('HomeController@editEmployee',$user['id']) }}" class="btn btn-primary">Edit</a>
                        <a href="{{ action('HomeController@deleteEmployee',$user['id']) }}" class="btn btn-danger">Delete</a>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endsection