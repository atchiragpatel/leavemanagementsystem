@extends('layouts.app')
@section('content')
    <div class="container">
        <h3>Change your password</h3>

        <form class="mt-2" method="post" action="updateuserpassword">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <div class="form-group row">
                <label class="col-xs-2 col-form-label">Old Password</label>
                <div class="col-xs-10">
                    <input class="form-control" name="oldpassword" type="password">
                </div>
            </div>
            <div class="form-group row">
                <label class="col-xs-2 col-form-label">New Password</label>
                <div class="col-xs-10">
                    <input class="form-control" name="newpassword" type="password">
                </div>
            </div>
            <div class="form-group row">
                <label class="col-xs-2 col-form-label">Confirm Password</label>
                <div class="col-xs-10">
                    <input class="form-control" name="confirmpassword" type="password">
                </div>
            </div>
            <input type="submit" value="Save" class="btn btn-primary"/>
        </form>
    </div>
@endsection
