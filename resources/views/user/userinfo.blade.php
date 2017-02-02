@extends('layouts.app')
@section('content')
    <div class="container">
        <div class="tab-content">
            <div class="tab-pane active" id="userinfo" role="tabpanel">
                <form class="mt-2" method="POST" action="updateuserinfo">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <div class="form-group row">
                        <label for="example-text-input" class="col-xs-2 col-form-label">First Name</label>
                        <div class="col-xs-10">
                            <input class="form-control" type="text" name="fname"
                                   value="<?php echo $user = Auth::user()->fname; ?>"
                                   id="example-text-input" disabled>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="example-search-input" class="col-xs-2 col-form-label">Middle Name</label>
                        <div class="col-xs-10">
                            <input class="form-control" type="text" name="mname"
                                   value="<?php echo $user = Auth::user()->mname; ?>"
                                   id="example-search-input" disabled>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="example-search-input" class="col-xs-2 col-form-label">Last Name</label>
                        <div class="col-xs-10">
                            <input class="form-control" type="text" name="lname"
                                   value="<?php echo $user = Auth::user()->lname; ?>"
                                   id="example-search-input" disabled>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="example-email-input" class="col-xs-2 col-form-label">Email</label>
                        <div class="col-xs-10">
                            <input class="form-control" type="email" name="email"
                                   value="<?php echo $user = Auth::user()->email; ?>"
                                   id="example-email-input" disabled>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="example-url-input" class="col-xs-2 col-form-label">Password</label>
                        <div class="col-xs-8">
                            <input class="form-control" type="password" name="password"
                                   value="<?php echo $user = Auth::user()->password; ?>"
                                   id="example-url-input" disabled>
                        </div>
                        <div class="col-xs-2">
                            <a href="{{ action('UserController@changeUserPassword') }}"
                               class="btn btn-primary">Update
                                Password</a>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-xs-2 col-form-label">DOJ</label>
                        <div class="col-xs-10">
                            <input class="form-control" name="doj" type="date"
                                   value="<?php echo $user = Auth::user()->doj; ?>"
                                   id="example-url-input" disabled>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-xs-2 col-form-label">Blood Group</label>
                        <div class="col-xs-10">
                            <input class="form-control" name="blood_group" type="varchar"
                                   value="<?php echo $user = Auth::user()->blood_group; ?>"
                                   id="example-url-input">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-xs-2 col-form-label">DOB</label>
                        <div class="col-xs-10">
                            <input class="form-control" name="dob" type="date"
                                   value="<?php echo $user = Auth::user()->dob; ?>"
                                   id="example-url-input">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-xs-2 col-form-label">Contact Number</label>
                        <div class="col-xs-10">
                            <input class="form-control" name="contact_number" type="varchar"
                                   value="<?php echo $user = Auth::user()->contact_number; ?>"
                                   id="example-url-input">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-xs-2 col-form-label">City</label>
                        <div class="col-xs-10">
                            <input class="form-control" name="city" type="varchar"
                                   value="<?php echo $user = Auth::user()->city; ?>"
                                   id="example-url-input">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-xs-2 col-form-label">State</label>
                        <div class="col-xs-10">
                            <input class="form-control" name="state" type="varchar"
                                   value="<?php echo $user = Auth::user()->state; ?>"
                                   id="example-url-input">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-xs-2 col-form-label">Country</label>
                        <div class="col-xs-10">
                            <input class="form-control" name="country" type="varchar"
                                   value="<?php echo $user = Auth::user()->country; ?>"
                                   id="example-url-input">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-xs-2 col-form-label">Zipcode</label>
                        <div class="col-xs-10">
                            <input class="form-control" name="zipcode" type="varchar"
                                   value="<?php echo $user = Auth::user()->zipcode; ?>"
                                   id="example-url-input">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-xs-2 col-form-label">Address</label>
                        <div class="col-xs-10">
                            <input class="form-control" name="address" type="text"
                                   value="<?php echo $user = Auth::user()->address; ?>"
                                   id="example-url-input">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-xs-2 col-form-label">Emergency Contact Name</label>
                        <div class="col-xs-10">
                            <input class="form-control" name="emergency_contact_name" type="varchar"
                                   value="<?php echo $user = Auth::user()->emergency_contact_name; ?>"
                                   id="example-url-input">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-xs-2 col-form-label">Emergency Contact Number</label>
                        <div class="col-xs-10">
                            <input class="form-control" name="emergency_contact_number" type="varchar"
                                   value="<?php echo $user = Auth::user()->emergency_contact_number; ?>"
                                   id="example-url-input">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-xs-2 col-form-label">Emergency Contact Relation</label>
                        <div class="col-xs-10">
                            <input class="form-control" name="emergency_contact_relation" type="varchar"
                                   value="<?php echo $user = Auth::user()->emergency_contact_relation; ?>"
                                   id="example-url-input">
                        </div>
                    </div>
                    <input type="submit" value="Update" class="btn btn-primary"/>
                </form>
            </div>
        </div>
    </div>
@endsection