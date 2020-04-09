<?php

namespace Tests\Unit\Helpers;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

use DateTime;
use App\Helpers\DatetimeOperations;


class DatetimeOperationsTest extends TestCase
{
    public function testNextThurdayAfter18(){
        $response = DatetimeOperations::nextThurdayAfter18(202005);
        $this->assertSame('2020-05-21', $response->format('Y-m-d'));
    }
    public function testWorkingDaysAfterClose(){
        $date = new Datetime('2020-05-15');
        $response = DatetimeOperations::WorkingDaysAfterClose($date);
        $this->assertSame('2020-05-29', $response->format('Y-m-d'));
    }
    public function testLastThursdayOfMonth(){
        $response = DatetimeOperations::lastThursdayOfMonth(202005);
        $this->assertSame('2020-05-28', $response->format('Y-m-d'));
    }
    public function testSecondMondayOfNextMonth(){
        $date = new Datetime('2020-04-15');
        $response = DatetimeOperations::secondMondayOfNextMonth($date);
        $this->assertSame('2020-05-11', $response->format('Y-m-d'));
    }
    public function testPreviousSettlementDate(){
        $response = DatetimeOperations::previousSettlementDate(202005);
        $this->assertSame(202004, $response);
    }
    public function testNextSettlementDate(){
        $response = DatetimeOperations::nextSettlementDate(202005);
        $this->assertSame(202006, $response);
    }

}

