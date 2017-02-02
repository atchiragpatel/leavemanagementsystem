@extends('layouts.app')
@section('content')
    <div class="container">
        <h2>Update Holiday</h2>
        <form method="post" action="/storeeditholiday/{{$holiday->id}}" class="inline-form">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <div class="form-group row">
                <label class="col-xs-2 col-form-label">Festival Name</label>
                <div class="col-xs-10">
                    <input type="text" name="festivalname" value="<?php echo $holiday['festivalname'] ?>" class="form-control"
                           placeholder="Enter holiday name">
                </div>
            </div>

            <div class="form-group row">
                <label class="col-xs-2 col-form-label">Festival Date</label>
                <div class="col-xs-10">
                    <input type="date" name="festivaldate" value="<?php echo $holiday['festivaldate'] ?>" class="form-control"
                           placeholder="Enter holiday date">
                </div>
            </div>
            <input type="submit" value="Update" class="btn btn-primary"/>
        </form>
    </div>
@endsection