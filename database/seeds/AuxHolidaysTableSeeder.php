<?php

use Illuminate\Database\Seeder;

class AuxHolidaysTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('aux_holidays')->delete();
        
        \DB::table('aux_holidays')->insert(array (
            0 => 
            array (
                'id' => 1,
                'date' => '2020-03-24',
                'holiday' => 'Día de la memoria',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            1 => 
            array (
                'id' => 2,
                'date' => '2020-03-03',
            'holiday' => 'Malvinas (Puente)',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            2 => 
            array (
                'id' => 3,
                'date' => '2020-03-02',
                'holiday' => 'Malvinas',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            3 => 
            array (
                'id' => 4,
                'date' => '2020-05-25',
                'holiday' => 'Revolución de Mayo',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
        ));
        
        
    }
}