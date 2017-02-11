<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of the routes that are handled
| by your application. Just tell Laravel the URIs it should respond
| to using a Closure or controller method. Build something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});
Auth::routes();

//Employee Routes
Route::get('/home', 'HomeController@index');
Route::get('/addemployee', 'HomeController@addEmployee');
Route::post('/employeeDetails', 'HomeController@employeeDetails');
Route::get('/listemployee', 'HomeController@listEmployee');
Route::get('/deleteemployee/{id}', 'HomeController@deleteEmployee');
Route::get('/editemployee/{id}', 'HomeController@editEmployee');
Route::post('/storeeditemployee/{id}', 'HomeController@storeEditEmployee');

//Holidays Routes
Route::get('/listholidays', 'HolidayController@listHolidays');
Route::get('/addholidays', 'HolidayController@addHolidays');
Route::post('/holidayDetails', 'HolidayController@holidayDetails');
Route::get('/editholiday/{id}', 'HolidayController@editHoliday');
Route::post('/storeeditholiday/{id}', 'HolidayController@storeEditHoliday');
Route::get('/deleteholiday/{id}', 'HolidayController@deleteHoliday');

//Leave Policy Route
Route::get('/leavepolicy', 'HomeController@leavepolicy');

//User Route
Route::get('/userinfo', 'UserController@userInfo');
Route::post('/updateuserinfo', 'UserController@updateUserInfo');
Route::get('/changeuserpassword', 'UserController@changeUserPassword');
Route::post('/updateuserpassword', 'UserController@updateUserPassword');
Route::get('/userbankdetail', 'UserController@userBankDetails');
Route::get('/addbankdetails', 'UserController@addBankDetails');
Route::post('/storebankdetails', 'UserController@storeBankDetails');
Route::get('/deletebankdetails/{id}', 'UserController@deleteBankDetails');
Route::get('/updatebankdetails/{id}', 'UserController@updateBankDetails');
Route::post('/storeupdatedbankdetails/{id}', 'UserController@storeUpdatedBankDetails');

//User Documents Routes
//Route::get('/listofdocuments', 'UserDocumnets@DocumnetsList');

//User Leave Routes
Route::get('/applyforleave', 'LeaveTransactionController@applyForLeave');
Route::post('/submitleave', 'LeaveTransactionController@submitLeave');

/*
 * This Route is for admin where admin can see the leave request
 */
Route::get('/leaverequest', 'LeaveTransactionController@leaveRequest');
Route::get('/leaveapprove/{id}', 'LeaveTransactionController@leaveApprove');
Route::get('/leavereject/{id}', 'LeaveTransactionController@leaveReject');
/*
 * Ajax Route
 */
Route::get('/applyforleave-response', 'LeaveTransactionController@isSandwitch');

