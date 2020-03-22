<?php
namespace App\Helpers;

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

}