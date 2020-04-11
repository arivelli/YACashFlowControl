<?php

namespace Tests\Unit;

use App\AppAccount;
use App\AppAccountPeriod;
use App\AppOperation;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Http\Controllers\Processes\CreditCardSummaries;
use DateTime;

class CreditCardSummariesTest extends TestCase
{
    public function testGetAccountPeriodOfVisa() {
        $account = AppAccount::find(4);
        $CCSummary = new CreditCardSummaries($account);
        $operation = new AppOperation;
        $operation->estimated_date = new Datetime('2025-05-12');
        $operation->settlement_date = 202505;
        $response = $CCSummary->getAccountPeriodOfOperation($operation);
        $expected = [
            'account_id' => $account->id,
            'settlement_date' => 202506,
            'closed_date' => '2025-05-29', //lastThursdayOfMonth
            'estimated_date' => '2025-06-09' //secondMondayOfNextMonth
        ];
        $this->assertDatabaseHas('app_accounts_periods', [
            'account_id' => $expected['account_id'],
            'settlement_date' => $expected['settlement_date'],
            'closed_date' => $expected['closed_date'],
            'estimated_date' => $expected['estimated_date'],
        ]);
        $this->assertInstanceOf(AppAccountPeriod::class, $response);
        $this->assertEquals($response->account->id, $expected['account_id']);
        $this->assertEquals($response->settlement_date, $expected['settlement_date']);
        $this->assertEquals($response->closed_date, $expected['closed_date']);
        $this->assertEquals($response->estimated_date, $expected['estimated_date']);

        $period = $CCSummary->getPeriodFromId($response->id);
        $expected = [
            'from' => '2025-04-24',
            'to' => '2025-05-29',
            'settlement_date' => 202506,
            'estimated_date' => '2025-06-09'
        ];
        $this->assertArraySubset($expected, $period);
    }
    public function testGetAccountPeriodOfMaster() {
        $account = AppAccount::find(11);
        $CCSummary = new CreditCardSummaries($account);
        $operation = new AppOperation;
        $operation->estimated_date = new Datetime('2025-05-12');
        $operation->settlement_date = 202505;
        $response = $CCSummary->getAccountPeriodOfOperation($operation);
        $expected = [
            'account_id' => $account->id,
            'settlement_date' => 202506,
            'closed_date' => '2025-05-22', //nextThurdayAfter18
            'estimated_date' => '2025-06-05' //workingDaysAfterClose
        ];
        $this->assertDatabaseHas('app_accounts_periods', [
            'account_id' => $expected['account_id'],
            'settlement_date' => $expected['settlement_date'],
            'closed_date' => $expected['closed_date'],
            'estimated_date' => $expected['estimated_date'],
        ]);
        $this->assertInstanceOf(AppAccountPeriod::class, $response);
        $this->assertEquals($expected['account_id'], $response->account->id );
        $this->assertEquals($expected['settlement_date'], $response->settlement_date);
        $this->assertEquals($expected['closed_date'], $response->closed_date);
        $this->assertEquals($expected['estimated_date'], $response->estimated_date);

        $period = $CCSummary->getPeriodFromId($response->id);
        $expected = [
            'from' => '2025-04-24',
            'to' => '2025-05-22',
            'settlement_date' => 202506,
            'estimated_date' => '2025-06-05'
        ];
        $this->assertArraySubset($expected, $period);
    }
    public function testGetPeriodFromOperationDateVisa() {
        $account = AppAccount::find(4);
        $CCSummary = new CreditCardSummaries($account);
        $operation = new AppOperation;
        $operation->estimated_date = new Datetime('2020-05-12');
        $operation->settlement_date = 202005;
        $period = $CCSummary->getPeriodFromOperation($operation);
        $expected = [
            'from' => '2020-04-30',
            'to' => '2020-05-28',
            'settlement_date' => 202006,
            'estimated_date' => '2020-06-08'
        ];
        $this->assertArraySubset($expected, $period);
    }
    public function testGetPeriodFromOperationDateMaster() {
        $account = AppAccount::find(11);
        $CCSummary = new CreditCardSummaries($account);
        $operation = new AppOperation;
        $operation->estimated_date = new Datetime('2020-05-12');
        $operation->settlement_date = 202005;
        $period = $CCSummary->getPeriodFromOperation($operation);
        $expected = [
            'from' => '2020-04-23',
            'to' => '2020-05-21',
            'settlement_date' => 202006,
            'estimated_date' => '2020-06-05'
        ];
        $this->assertArraySubset($expected, $period);
    }

}
