<?php
namespace App\Helpers;

use DateTime;

setlocale(LC_ALL, 'es_AR.utf8');
class Format {
    /**
     * Format a Integet to a money type
     * 
     * @param Int $n number to format
     * @param String $m currency symbol
     */
    static public function int2money(Int $n = null, String $m = '$'){
        if (null !== $n) {
            return $m . ' ' . number_format($n/100,2,',','.');
        } else {
            return '';
        }
    }
    /**
     * Get the Integer part of a formmated money type
     * 
     * @param String $m
     */
    static public function money2int(String $m){
        return filter_var($m, FILTER_SANITIZE_NUMBER_INT);
    }
    /**
    * Get the number of the week on the month
    *
    * @param Datetime $date
    *
    * @return Int
    */
    static function get_week_of_month($date)
	{
		$day_of_month			= $date->format("j");
		$day_of_week			= $date->format("N");
		$first_day_of_month		= new DateTime($date->format("Y-m-01"));
		$week_of_month			= ceil($day_of_month / 7);
		//Si el día de la semana del primer día del mes es mayor que el día de la semana de la fecha, incremento la semana del mes
		if ($first_day_of_month->format("N") > $day_of_week) {
			$week_of_month++;
		}
		return (int) $week_of_month;
    }
    /**
     * Convert a settlement_date to Datetime
     * 
     * @param Int $st
     */
    static function settlement_date2date(int $st){
        $year = substr($st,0,4);
        $month = substr($st,-2);
        return new DateTime("{$year}-{$month}-01");
    }
    /**
     * Convert a settlement date to a humman readable version MONTH YEAR
     * 
     * @param Int $st
     * 
     * @return String
     */
    static function settlement_date2Period(int $st){
        $year = substr($st,0,4);
        $month = substr($st,4,2);
        return strtoupper(strftime('%B %Y', (new DateTime("{$year}-{$month}-01"))->format('U')));
    }
    /**
     * Convert a date to settlement_date
     * 
     * @param Datetime $date
     * 
     * @return Int
     */
    static function date2settlement_date(Datetime $date){
        return (int) $date->format('Ym');
    }

}