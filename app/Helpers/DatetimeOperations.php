<?php

namespace App\Helpers;

setlocale(LC_ALL, 'es_AR.utf8');

use DateTime;
use DateInterval;
use Illuminate\Support\Facades\Date;

class DatetimeOperations
{
    /**
     * Add or substract working days to a date
     * 
     * @param Datetime $date
     * @param String $method add|sub
     * @param Int $days 
     * 
     * @return Datetime
     */
    static public function moveWorkingDays(Datetime $date, string $method, int $days)
    {
        $newDate = clone $date;
        //dd($newDate);
        $holidays = \App\AuxHoliday::where([
            ['date', '>=', $newDate->format('Y') . '-01-01'],
            ['date', '<=', $newDate->format('Y')+5 . '-12-31'],
        ])->pluck('date')->all();
        for ($j = 0; $j < $days; $j++) {
            $newDate->$method(new DateInterval('P1D'));
            if ($newDate->format('N') > 5 || in_array($newDate->format('Y-m-d'), $holidays)) {
                $j--;
            }
        }
        return $newDate;
    }
    /**
     * Look for the first Thursday after 18th of the received settlement date (Mastercard close_pattern)
     * THE SETTLEMENT_DATE SHOULD BE BASED ON CLOSED DATE
     * 
     * @param int $st
     * 
     * @return Datetime
     */
    static function nextThurdayAfter18(int $st)
    {
        $date = Format::settlement_date2date($st);
        //Previous month
        //$date->sub(new DateInterval('P1M'));
        //Of day 19th
        $date->setDate($date->format('Y'), $date->format('m'), 19);
        //Adding day by day up to the next Thurday
        while ($date->format('N') != 4) {
            $date->add(new DateInterval('P1D'));
        }
        return $date;
    }
    /**
     * Calculate 10 working days from the received date (Mastercard due_pattern)
     * 
     * @param Datetime $date
     */
    static function workingDaysAfterClose(Datetime $date)
    {
        return DatetimeOperations::moveWorkingDays($date, 'add', 10);
    }

    /**
     * Last Thursday of the month year for (VISA close_pattern)
     * * THE SETTLEMENT_DATE SHOULD BE BASED ON CLOSED DATE
     * 
     * @param Int $st
     * 
     * @return Datetime
     */
    static function lastThursdayOfMonth(int $st){
        $date = Format::settlement_date2date($st);
        //Previous month
        //$date->sub(new DateInterval('P1M'));
        return (new Datetime)->setTimestamp ( strtotime('last thursday of '. $date->format('M Y') ) );
    }

    /**
     * Second Monday of Next Month (VISA due_pattern)
     * 
     * @param Datetime $date
     */
    static function secondMondayOfNextMonth(Datetime $date){
        $newDate = clone $date;
        $newDate->add(new DateInterval('P1M'));
        return (new Datetime)->setTimestamp( strtotime('second monday of '. $newDate->format('M Y') ) );
    }
    /**
     * Settlement Date of next period
     */
    static function nextSettlementDate(int $st){
        $date = Format::settlement_date2date($st)->add(new DateInterval('P1M'));
        return Format::date2settlement_date($date);
    }
    /**
     * Settlement Date of next period
     */
    static function previousSettlementDate(int $st){
        $date = Format::settlement_date2date($st)->sub(new DateInterval('P1M'));
        return Format::date2settlement_date($date);
    }
}
