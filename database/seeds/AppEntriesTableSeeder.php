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
            ),
            2 => 
            array (
                'id' => 3,
                'entry_type' => 3,
                'category_id' => 20,
                'area_id' => 6,
                'date' => '2020-04-06',
                'concept' => 'Mesa de pool',
                'currency' => '$',
                'real_amount' => 12000000,
                'one_pay_amount' => 12000000,
                'dollar_value' => 6426,
                'is_extraordinary' => 1,
                'affect_capital' => 1,
                'notes' => NULL,
                'plan' => NULL,
                'is_done' => 0,
                'created_at' => '2020-04-06 23:15:17',
                'created_by' => 0,
            ),
            3 => 
            array (
                'id' => 4,
                'entry_type' => 3,
                'category_id' => 20,
                'area_id' => 6,
                'date' => '2020-04-06',
                'concept' => 'Celu Adri',
                'currency' => '$',
                'real_amount' => 2000000,
                'one_pay_amount' => 2000000,
                'dollar_value' => 6426,
                'is_extraordinary' => 1,
                'affect_capital' => 0,
                'notes' => NULL,
                'plan' => NULL,
                'is_done' => 0,
                'created_at' => '2020-04-06 23:16:02',
                'created_by' => 0,
            ),
        ));
        
        
    }
}