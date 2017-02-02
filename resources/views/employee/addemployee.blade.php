@extends('layouts.app')

@section('content')
    <div class="container">
        <form method="post" action="employeeDetails"  class="inline-form">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <div class="form-group row">

                <label class="col-xs-2 col-form-label">First Name</label>
                <div class="col-xs-10">
                    <input type="text" name="fname" class="form-control" placeholder="Enter employee first Name">
                </div>
            </div>

            <div class="form-group row">
                <label class="col-xs-2 col-form-label">Middle Name</label>
                <div class="col-xs-10">
                    <input type="text" name="mname" class="form-control" placeholder="Enter employee middle name">
                </div>
            </div>

            <div class="form-group row">
                <label class="col-xs-2 col-form-label">Last Name</label>
                <div class="col-xs-10">
                    <input type="text" name="lname" class="form-control" placeholder="Enter employee last name">
                </div>
            </div>

            <div class="form-group row">
                <label class="col-xs-2 col-form-label">Salary</label>
                <div class="col-xs-10">
                    <input type="varchar" name="salary" class="form-control" placeholder="Enter employee salary per month">
                </div>
            </div>

            <div class="form-group row">
                <label class="col-xs-2 col-form-label">Joining Date</label>
                <div class="col-xs-10">
                    <input type="date" name="doj" class="form-control" placeholder="Enter employee joining date">
                </div>
            </div>

            <div class="form-group row">
                <label class="col-xs-2 col-form-label">Email</label>
                <div class="col-xs-10">
                    <input type="email" name="email" class="form-control" placeholder="Enter employee email">
                </div>
            </div>

            <div class="form-group row">
                <label class="col-xs-2 col-form-label">Password</label>
                <div class="col-xs-10">
                    <input type="password" name="password" class="form-control" placeholder="Enter employee password">
                </div>
            </div>

            <input type="submit" value="Save" class="btn btn-primary"/>
        </form>
    </div>
@endsection