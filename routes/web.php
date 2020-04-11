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
    return redirect('/admin/cashFlow');
});

//Group for admin directory
Route::group([
    'middleware' => ['web', '\arivelli\crudbooster\middlewares\CBBackend'],
    'prefix' => config('crudbooster.ADMIN_PATH')
   // 'namespace' => 'App\Http\Controllers',
],  function () {
    //CASHFLOW
    Route::get('/cashFlow','AdminAppOperationsController@cashFlow')->name('cashFlow');
    Route::get('/cashFlow/{filter}','AdminAppOperationsController@cashFlow')->name('cashFlowWithFilter');
    Route::get('/cashFlowData/{settlement_date}','AdminAppOperationsController@cashFlowData')->name('cashFlowData');

    //ENTRIES
    Route::post('/app_entries/preview_plan','AdminAppEntriesController@preview_plan')->name('entries_preview_plan');
    //Route::get('/testDates','AdminAppEntriesController@testDates')->name('testDates');

    //PLANS
    Route::get('/app_plans/getPlanByEntryId/{entry_id}','AdminAppPlansController@getPlanByEntryId')->name('plans_getPlanByEntryId');

    //OPERATIONS
    Route::post('/app_operations/execute/{operation_id}','AdminAppOperationsController@executeOperation')->name('executeOperation');
    //Route::get('/compute_operations/{entry_id}','AdminAppEntriesController@hook_after_add_child')->name('compute_operations');

    Route::get('/dollarValue/update','ManageDollarValue@update_table')->name('dollarValue_updateTable');
    Route::get('/dollarValue/getvalueof/{date?}','ManageDollarValue@get_value_of')->name('dollarValue_getValueOf');

    //ACCOUNTS
    Route::post('/app_accounts/setLastUpdateAmount','AdminAppAccountsController@setLastUpdateAmount')->name('setLastUpdateAmount');
    Route::get('/app_accounts_periods/operations/{id}','AdminAppAccountsPeriodsController@getOperationsByPeriod')->name('account_periods_getOperationsByPeriod');
    Route::get('/app_accounts_periods/updatePeriod/{id}','AdminAppAccountsPeriodsController@updatePeriod')->name('account_periods_updatePeriod');

    //REPORTS
    Route::get('/reports/balance/{year?}','AdminAppReportsController@balances')->name('reports_balances');
});
