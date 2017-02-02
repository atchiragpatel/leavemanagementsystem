@extends('layouts.app')
@section('content')
    <div class="container">
        <h2>Update Employee</h2>
        <form method="post" action="/storeeditemployee/{{$user->id}}" class="inline-form">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <div class="form-group row">
                <label class="col-xs-2 col-form-label">First Name</label>
                <div class="col-xs-10">
                    <input type="text" name="fname" value="<?php echo $user['fname'] ?>" class="form-control"
                           placeholder="Enter employee first Name">
                </div>
            </div>

            <div class="form-group row">
                <label class="col-xs-2 col-form-label">Middle Name</label>
                <div class="col-xs-10">
                    <input type="text" name="mname" value="<?php echo $user['mname'] ?>" class="form-control"
                           placeholder="Enter employee middle name">
                </div>
            </div>

            <div class="form-group row">
                <label class="col-xs-2 col-form-label">Last Name</label>
                <div class="col-xs-10">
                    <input type="text" name="lname" value="<?php echo $user['lname'] ?>" class="form-control"
                           placeholder="Enter employee last name">
                </div>
            </div>

            <div class="form-group row">
                <label class="col-xs-2 col-form-label">Salary</label>
                <div class="col-xs-10">
                    <input type="varchar" name="salary" value="<?php echo $user['salary'] ?>" class="form-control"
                           placeholder="Enter employee salary per month">
                </div>
            </div>

            <div class="form-group row">
                <label class="col-xs-2 col-form-label">Joining Date</label>
                <div class="col-xs-10">
                    <input type="date" name="doj" value="<?php echo $user['doj'] ?>" class="form-control"
                           placeholder="Enter employee date of joining">
                </div>
            </div>

            <div class="form-group row">
                <label class="col-xs-2 col-form-label">Email</label>
                <div class="col-xs-10">
                    <input type="email" name="email" value="<?php echo $user['email'] ?>" class="form-control"
                           placeholder="Enter employee email">
                </div>
            </div>
            <input type="submit" value="Save" class="btn btn-primary"/>
        </form>
    </div>
@endsection