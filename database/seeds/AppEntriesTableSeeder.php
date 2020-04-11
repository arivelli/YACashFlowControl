<?php

use Illuminate\Database\Seeder;

class AppEntriesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS = 0;');

        \DB::table('app_entries')->delete();
        
        \DB::table('app_entries')->insert(array (
            0 => 
            array (
                'id' => 1,
                'entry_type' => 2,
                'category_id' => 27,
                'area_id' => 6,
                'date' => '2020-04-06',
                'concept' => 'Pago de tarjeta Visa Platino $',
                'currency' => '$',
                'real_amount' => 100000,
                'one_pay_amount' => 1000000,
                'dollar_value' => 6426,
                'is_extraordinary' => 0,
                'affect_capital' => 0,
                'notes' => NULL,
                'plan' => NULL,
                'is_done' => 0,
                'created_at' => '2020-04-06 23:13:13',
                'created_by' => 0,
            ),
            1 => 
            array (
                'id' => 2,
                'entry_type' => 2,
                'category_id' => 27,
                'area_id' => 6,
                'date' => '2020-04-06',
                'concept' => 'Pago de tarjeta Mastercard',
                'currency' => '$',
                'real_amount' => 100000,
                'one_pay_amount' => 100000,
                'dollar_value' => 6426,
                'is_extraordinary' => 0,
                'affect_capital' => 0,
                'notes' => NULL,
                'plan' => NULL,
                'is_done' => 0,
                'created_at' => '2020-04-06 23:13:57',
                'created_by' => 0,
            )
        ));
        DB::statement('SET FOREIGN_KEY_CHECKS = 1;');
    }
}