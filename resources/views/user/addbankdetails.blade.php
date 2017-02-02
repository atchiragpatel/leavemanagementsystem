@extends('layouts.app')
@section('content')
    <div class="container">
        <h2>Add Bank Details</h2>
        <form method="post" action="storebankdetails" class="inline-form">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <div class="form-group row">
                <label class="col-xs-2 col-form-label">Bank Name</label>
                <div class="col-xs-10">
                    <input type="text" name="bank_name" class="form-control" placeholder="Enter bank name">
                </div>
            </div>

            <div class="form-group row">
                <label class="col-xs-2 col-form-label">Branch Name</label>
                <div class="col-xs-10">
                    <input type="text" name="branch_name" class="form-control" placeholder="Enter bank branch name">
                </div>
            </div>

            <div class="form-group row">
                <label class="col-xs-2 col-form-label">Bank Account Number</label>
                <div class="col-xs-10">
                    <input type="number" name="account_number" class="form-control"
                           placeholder="Enter bank account number">
                </div>
            </div>

            <div class="form-group row">
                <label class="col-xs-2 col-form-label">Bank IFSC Code</label>
                <div class="col-xs-10">
                    <input type="varchar" name="ifsc_code" class="form-control" placeholder="Enter bank ifsc code">
                </div>
            </div>

            <input type="submit" value="Save" class="btn btn-primary"/>
        </form>
    </div>
@endsection