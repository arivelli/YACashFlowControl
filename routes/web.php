<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return redirect('/cashFlow');
});

Route::get('/compute_operations/{entry_id}','AdminAppEntriesController@hook_after_add_child')->name('compute_operations');


Route::get('/admin/cashFlow','AdminAppOperationsController@cashFlow')->name('cashFlow');
Route::get('/admin/cashFlow/{settlement_date}','AdminAppOperationsController@cashFlow')->name('cashFlowWithDate');
Route::get('/admin/cashFlowData/{settlement_date}','AdminAppOperationsController@cashFlowData')->name('cashFlowData');

Route::post('/admin/app_operations/execute/{operation_id}','AdminAppOperationsController@execute_operation')->name('execute_operation');

Route::get('/admin/dollarValue/update','ManageDollarValue@update_table')->name('dollarValue_updateTable');
Route::get('/admin/dollarValue/getvalueof/{date?}','ManageDollarValue@get_value_of')->name('dollarValue_getValueOf');

Route::post('/admin/app_accounts/setLastUpdateAmount','AdminAppAccountsController@setLastUpdateAmount')->name('setLastUpdateAmount');



Route::get('/admin/testDates','AdminEntriesController@testDates')->name('testDates');
