@extends('layouts.app')

@section('content')
    <div class="container">
        <form method="post" action="holidayDetails" class="inline-form">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <div class="form-group row">
                <label class="col-xs-2 col-form-label">Festival Name</label>
                <div class="col-xs-10">
                    <input type="text" name="festivalname" class="form-control" placeholder="Enter Festival Name">
                </div>
            </div>

            <div class="form-group row">
                <label class="col-xs-2 col-form-label">Festival Date</label>
                <div class="col-xs-10">
                    <input type="date" name="festivaldate" class="form-control">
                </div>
            </div>
            <input type="submit" value="Save" class="btn btn-primary"/>
        </form>
    </div>
@endsection