@extends('layouts.app')
@section('content')
    <div class="container">
        @if(Auth::user()->role == 'ADMIN')
            <div class="panel-body">
                Create Festival Holidays <a href="addholidays" class="btn btn-primary pull-right mb-2 mt-2">Add
                    Holiday</a>
            </div>
        @endif
        <table class="table">
            <thead>
            <tr>
                <th>Festival Name</th>
                <th>Day</th>
                <th>Month</th>
                <th>Date</th>
                <th>&nbsp;</th>
            </tr>
            </thead>
            <tbody>
            @foreach($holidays as $holiday)
                <?php
                $date = $holiday['festivaldate'];
                ?>
                <tr>
                    <td><?php echo $holiday['festivalname'] ?></td>
                    <td><?php echo date('l', strtotime($date));
                        ?></td>
                    <td><?php echo date('F', strtotime($date));
                        ?></td>
                    <td><?php echo $holiday['festivaldate'] ?></td>
                    @if(Auth::user()->role == 'ADMIN')
                        <td>
                            <a href="{{ action('HolidayController@editHoliday',$holiday['id']) }}" class="btn btn-primary">Edit</a>
                            <a href="{{ action('HolidayController@deleteHoliday',$holiday['id']) }}" class="btn btn-danger">Delete</a>
                        </td>
                    @endif
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endsection