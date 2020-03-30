<?php
namespace App\Helpers;

setlocale(LC_ALL, 'es_AR.utf8');

use DateTime;

class Format {
    static public function int2money(Int $n = null, String $m = '$'){
        if (null !== $n) {
            return $m . ' ' . number_format($n/100,2,',','.');
        } else {
            return '';
        }
    }
    static public function money2int(String $m){
        return filter_var($m, FILTER_SANITIZE_NUMBER_INT);
    }

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
		return $week_of_month;
    }
    static function settlement_date2date($st){
        $year = substr($st,0,4);
        $month = substr($st,4,2);
        return new DateTime("{$year}-{$month}-01");
    }
    static function settlement_date2Period($st){
        $year = substr($st,0,4);
        $month = substr($st,4,2);
        //return (new DateTime("{$year}-{$month}-01"))->format('d-m-Y');
        return strtoupper(strftime('%B %Y', (new DateTime("{$year}-{$month}-01"))->format('U')));
    }
}