@extends('layouts.app')
@section('content')
    <div class="container">
        <div class="d-inline-block w-100">
            <div class="float-xs-left">
                <h3>Request for leave</h3>
            </div>
            <div class="float-xs-right text-xs-center" style="color: #ffffff">
                <div class="d-inline-block bg-primary p-1">
                    <h4 class="m-0" id="sickleave">
                        <?php echo $sickLeave; ?>
                    </h4>
                    Sick Leave
                </div>
                <div class="d-inline-block bg-primary p-1" id="casualleave">
                    <h4 class="m-0"><?php echo $casualleave; ?></h4>
                    Casual Leave
                </div>
                <div class="d-inline-block bg-primary p-1" id="privilegeleave">
                    <h4 class="m-0"><?php echo $privilegeleave; ?></h4>
                    Privilege Leave
                </div>
            </div>
        </div>
        <form method="post" action="submitleave" class="inline-form mt-1">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <div class="form-group row">
                <label class="col-xs-2 col-form-label">Leave Type</label>
                <div class="col-xs-10">
                    <select id="leave_type" name="leave_type" class="form-control">
                        <option>Select leave type</option>
                        <option>SICK LEAVE</option>
                        <option>CASUAL LEAVE</option>
                        <option>PRIVILEGE LEAVE</option>
                        <option>LEAVE WITHOUT PAY</option>
                    </select>
                    @if ($errors->has('leave_type'))
                        <span class="help-block">
                            <strong>{{ $errors->first('leave_type') }}</strong>
                        </span>
                    @endif
                </div>
            </div>

            <div class="form-group row">
                <label class="col-xs-2 col-form-label">From Date</label>
                <div class="col-xs-10">
                    <input type="date" id="from_date" name="from_date" class="form-control">
                    @if ($errors->has('from_date'))
                        <span class="help-block">
                            <strong>{{ $errors->first('from_date') }}</strong>
                        </span>
                    @endif
                </div>
            </div>

            <div class="form-group row">
                <label class="col-xs-2 col-form-label">To Date</label>
                <div class="col-xs-10">
                    <input type="date"
                           id="to_date"
                           onfocusout="days_between(getElementById('from_date').value , getElementById('to_date').value, 'total_days'); isSandwitch()"
                           name="to_date"
                           class="form-control">
                    @if ($errors->has('to_date'))
                        <span class="help-block">
                            <strong>{{ $errors->first('to_date') }}</strong>
                        </span>
                    @endif
                </div>
            </div>

            <div class="form-group row">
                <label class="col-xs-2 col-form-label">Total Days</label>
                <div class="col-xs-10">
                    <input id="total_days" type="varchar" name="value" class="form-control" placeholder="Total Days"
                           disabled>
                </div>
            </div>

            <div class="form-group row">
                <label class="col-xs-2 col-form-label">Reason for Leave</label>
                <div class="col-xs-10">
                    <input type="text" name="user_comments" class="form-control" placeholder="Reason for Leave">
                    @if ($errors->has('user_comments'))
                        <span class="help-block">
                            <strong>{{ $errors->first('user_comments') }}</strong>
                        </span>
                    @endif
                </div>
            </div>

            <div class="form-group row">
                <div class="col-xs-2">
                    <input type="submit" value="Request" class="btn btn-primary"/>
                </div>
                <div class="col-xs-10" id="displayData">
                    <p>

                    </p>
                </div>
            </div>
        </form>
    </div>
    <script type="text/javascript">
        function days_between(from_date, to_date, targetId) {
            var date1 = new Date(from_date);
            var date2 = new Date(to_date);
            var timeDiff = Math.abs(date2.getTime() - date1.getTime());
            var diffDays = Math.ceil(timeDiff / (1000 * 3600 * 24));
            document.getElementById(targetId).value = diffDays + 1;
        }
    </script>
    <script>
        function isSandwitch() {
            $(document).ready(function () {
                $.ajax({
                    type: 'GET',
                    url: './applyforleave-response',
                    data: {
                        from_date: document.getElementById('from_date').value,
                        to_date: document.getElementById('to_date').value,
                        leave_type: document.getElementById('leave_type').value,
                        sick_leave: parseFloat(document.getElementById('sickleave').textContent),
                        casual_leave: parseFloat(document.getElementById('casualleave').textContent),
                        privilege_leave: parseFloat(document.getElementById('privilegeleave').textContent)
                    },
                    success: function (data) {
                        //alert(data);
                        $("#displayData").text(data);
                    }
                });
            });
        }
    </script>
@endsection