<?php
namespace App\Http\Controllers;

use DB;
use GuzzleHttp\Client;
use DateTime;

class ManageDollarValue extends \arivelli\crudbooster\controllers\CBController
{
    private $token = 'eyJhbGciOiJIUzUxMiIsInR5cCI6IkpXVCJ9.eyJleHAiOjE1OTk2NTUxMzMsInR5cGUiOiJleHRlcm5hbCIsInVzZXIiOiJhcml2ZWxsaUB3ZWJtaW5kLmNvbS5hciJ9.fnHUY1p-hvIf54-j-Q75ZLJ8A5TmxQ3LtDiYXKV0WfNC435hLZcZi3eTWu2lFEcWLyImwXzwwcjf9_8iLeJZ8g';
    
    private $token_expiration_date = '2020-09-09';

    /**
     * Keep the aux_dollar_value table updated with values from BCRA
     */
    public function update_table()
    {
        $tomorrow = new DateTime('tomorrow');
        $today = new DateTime('today');
        
        //Get the latest 5 records stored on the db
        $query = DB::table('aux_dollar_value')
            ->select('d')
            ->orderby('d','DESC')
            ->limit(5)
            ->get();

        //check for the tomorrow record to avoid to run the udpate more than one time per day
        if($query[0]->d < $tomorrow->format('Y-m-d') ) {

            //Get the values from BCRA
            $values = $this->get_values();

            //Filter the values by letting just the newest
            foreach($values as $dollar_value){
                if($dollar_value->d >= $query[3]->d){
                    $valid_dollar_values[] = $dollar_value;
                }
            }

            //Proceed only if there are data to update
            if(count($valid_dollar_values) > 0) {
                
                //Delete latest records
                $query = DB::table('aux_dollar_value')
                ->where('d','>=',$query[3]->d)
                ->delete();

                //Insert the newest records
                foreach($valid_dollar_values as $dollar_value){
                    DB::table('aux_dollar_value')
                    ->insert([
                        'd' => $dollar_value->d,
                        'v' => $dollar_value->v*100,
                    ]);
                    $last_value = $dollar_value;
                }
                //Insert a record for today
                if($today->format('Y-m-d') > $last_value->d){
                    DB::table('aux_dollar_value')
                    ->insert([
                        'd' => $today->format('Y-m-d'),
                        'v' => $last_value->v*100,
                    ]);
                }

                //Insert an empty record of tomorrow to avoid more than one update per day
                DB::table('aux_dollar_value')
                    ->insert([
                        'd' => $tomorrow->format('Y-m-d'),
                        'v' => 0,
                    ]);
            }
        } else {
            echo "no";
        }
    }

    /**
     * Get the all the values availables on BCRA
     * 
     * @retrun array
     */
    public function get_values(){
        $client = new Client();
        $api_response = $client->get('https://api.estadisticasbcra.com/usd_of_minorista', [
            'headers' => [
                'Authorization' => 'BEARER '. $this->token
            ]
        ]);
        return json_decode( (string) $api_response->getBody());
    }
    /**
     * 
     */
    public function get_token_expiration_date(){
        return $this->token_expiration_date;
    }

    static public function get_value_of($date = null){
        $date = $date !== null ? $date : (new DateTime())->format('Y-m-d');
        
        $query = DB::table('aux_dollar_value')
        ->where([
            ['d', '<=', $date],
            ['v', '>', 0]
        ])
        ->orderBy('d', 'DESC')
        ->first();
        return $query->v;
    }
}
