<?php

namespace Tests\Unit\Helpers;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

use DateTime;
use App\Helpers\Format;


class FormatTest extends TestCase
{
    public function testInt2money(){
        $response = Format::int2money(501262, 'U$S');
        $this->assertSame('U$S 5.012,62', $response);
    }
    public function testInt2money2(){
        $response = Format::int2money(null, 'U$S');
        $this->assertSame('', $response);
    }
    public function testMoney2int(){
        $response = Format::money2int('U$S 5012,62');
        $this->assertSame('501262', $response);
    }
    public function testGet_week_of_month(){
        $date = new Datetime('2020-05-05');
        $response = Format::get_week_of_month($date);
        $this->assertSame(2, $response);
    }
    public function testSettlement_date2date(){
        $response = Format::settlement_date2date(202005);
        $this->assertSame('2020-05-01', $response->format('Y-m-d'));
    }
    public function testSettlement_date2Period(){
        $response = Format::settlement_date2Period(202005);
        $this->assertSame('MAYO 2020', $response);
    }
    public function testDate2settlement_date(){
        $date = new Datetime('2020-05-01');
        $response = Format::date2settlement_date($date);
        $this->assertSame(202005, $response);
    }

}

