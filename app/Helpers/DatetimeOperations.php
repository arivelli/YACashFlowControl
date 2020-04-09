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
        $holidays = \App\AuxHoliday::where([
            ['date', '>', $date->format('Y')],
            ['date', '<', $date->format('Y')+2],
        ])->pluck('date')->all();
        for ($j = 0; $j < $days; $j++) {
            $date->$method(new DateInterval('P1D'));
            if ($date->format('N') > 5 || in_array($date->format('Y-m-d'), $holidays)) {
                $j--;
            }
        }
        return $date;
    }
    /**
     * Look for the first Thursday after 18th of the received settlement date
     * 
     * @param int $st
     */
    static function nextThurdayAfter18(int $st)
    {
        $year = substr($st, 0, 4);
        $month = substr($st, 4, 2);
        $date = new DateTime("{$year}-{$month}-19");
        while ($date->format('N') != 4) {
            $date = $date->add(new DateInterval('P1D'));
        }
        return $date;
    }
    /**
     * Calculate 10 working days from the received date
     * 
     * @param Datetime $date
     */
    static function workingDaysAfterClose(Datetime $date)
    {
        return DatetimeOperations::moveWorkingDays($date, 'add', 10);
    }
    /**
     * Last Thursday of the month year
     * 
     * @param Int $st
     */
    static function lastThursdayOfMonth(int $st){
        $date = Format::settlement_date2date($st);
        return (new Datetime)->setTimestamp ( strtotime('last thursday of '. $date->format('M Y') ) );
    }
    /**
     * Second Monday of Next Month
     * 
     * @param Datetime $date
     */
    static function secondMondayOfNextMonth(Datetime $date){
        $newDate = new Datetime($date->format('Y-m-d'));
        $newDate->add(new DateInterval('P1M'));
        return (new Datetime)->setTimestamp(  strtotime('second monday of '. $newDate->format('M Y') ) );
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
