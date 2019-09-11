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
    return view('welcome');
});
Route::get('/compute_operations/{entry_id}','AdminEntriesController@hook_after_add_child')->name('compute_operations');
Route::get('/cashFlow/{settlement_date}','AdminAppOperationsController@cashFlow')->name('cashFlow');
Route::get('/cashFlow','AdminAppOperationsController@cashFlow')->name('cashFlow');
Route::get('/cashFlowData/{settlement_date}','AdminAppOperationsController@cashFlowData')->name('cashFlowData');

Route::get('/admin/app_operations/execute/{entry_id}','AdminAppOperationsController@execute_operation')->name('execute_operation');

Route::get('updateDollarValues','ManageDollarValue@updateTable')->name('updateTable');
Route::get('testDates','AdminEntriesController@testDates')->name('testDates');
