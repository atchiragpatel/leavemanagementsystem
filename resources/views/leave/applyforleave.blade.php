@extends('layouts.app')
@section('content')
    <div class="container">
        <div class="d-inline-block w-100">
            <div class="float-xs-left">
                <h3>Request for leave</h3>
            </div>
            <div class="float-xs-right text-xs-center" style="color: #ffffff">
                <div class="d-inline-block bg-primary p-1">
                    <h4 class="m-0"><?php echo $sickLeave; ?></h4>
                    Sick Leave
                </div>
                <div class="d-inline-block bg-primary p-1">
                    <h4 class="m-0"><?php echo $casualleave; ?></h4>
                    Casual Leave
                </div>
                <div class="d-inline-block bg-primary p-1">
                    <h4 class="m-0"><?php echo $privilegeleave; ?></h4>
                    Privilege Leave
                </div>
            </div>
        </div>
        <form method="post" action="" class="inline-form mt-1">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <div class="form-group row">
                <label class="col-xs-2 col-form-label">Leave Type</label>
                <div class="col-xs-10">
                    <select id="disabledSelect" class="form-control">
                        <option>Select leave type</option>
                        <option>Casual Leave</option>
                        <option>Sick Leave</option>
                        <option>Privilege Leave</option>
                        <option>Leave Without Pay</option>
                    </select>
                </div>
            </div>

            <div class="form-group row">
                <label class="col-xs-2 col-form-label">From Date</label>
                <div class="col-xs-10">
                    <input type="datetime-local" name="from_date" class="form-control" placeholder="From">
                </div>
            </div>

            <div class="form-group row">
                <label class="col-xs-2 col-form-label">To Date</label>
                <div class="col-xs-10">
                    <input type="datetime-local" name="to_date" class="form-control" placeholder="To">
                </div>
            </div>

            <div class="form-group row">
                <label class="col-xs-2 col-form-label">Total Days</label>
                <div class="col-xs-10">
                    <input type="varchar" class="form-control" placeholder="Total Days" disabled>
                </div>
            </div>

            <div class="form-group row">
                <label class="col-xs-2 col-form-label">Reason for Leave</label>
                <div class="col-xs-10">
                    <input type="text" name="reasonforleave" class="form-control" placeholder="Reason for Leave">
                </div>
            </div>

            <input type="submit" value="Request" class="btn btn-primary"/>
        </form>
    </div>
@endsection