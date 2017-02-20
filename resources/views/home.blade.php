@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                @if(Auth::user()->role == 'ADMIN')
                    <div class="panel panel-default">
                        <div class="panel-heading">Admin Dashboard</div>
                        <div class="panel-body">
                            View Employees <a href="listemployee" class="btn btn-primary pull-right mb-2 mt-2">View
                                Employees</a>
                        </div>
                        <div class="panel-body">
                            View Festival Holidays <a href="listholidays" class="btn btn-primary pull-right mb-2 mt-2">View
                                Festival Holidays</a>
                        </div>
                        <div class="panel-body">
                            View Leave Requests <a href="leaverequest" class="btn btn-primary pull-right mb-2 mt-2">View Leave
                                Requests</a>
                        </div>
                    </div>
                @else
                    <div class="panel panel-default">
                        <div class="panel-heading">User Dashboard</div>
                        <div class="panel-body">
                            View User Info <a href="userinfo" class="btn btn-primary pull-right mb-2 mt-2">
                                User Info</a>
                        </div>
                        <div class="panel-body">
                            View User Bank Details <a href="userbankdetail" class="btn btn-primary pull-right mb-2 mt-2">
                                User Bank Details</a>
                        </div>
                        {{--<div class="panel-body">--}}
                            {{--View User Documents <a href="listofdocuments" class="btn btn-primary pull-right mb-2 mt-2">--}}
                                {{--User Documents</a>--}}
                        {{--</div>--}}
                        <div class="panel-body">
                            View Festival Holidays <a href="listholidays" class="btn btn-primary pull-right mb-2 mt-2">Festival Holidays</a>
                        </div>
                        <div class="panel-body">
                            Apply For Leave <a href="applyforleave" class="btn btn-primary pull-right mb-2 mt-2">Apply For Leave</a>
                        </div>
                        <div class="panel-body">
                            Applied Leaves <a href="appliedleaves" class="btn btn-primary pull-right mb-2 mt-2">Applied Leaves</a>
                        </div>
                        <div class="panel-body">
                            View Leave Policy <a href="leavepolicy" class="btn btn-primary pull-right mb-2 mt-2">Leave Policy</a>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
