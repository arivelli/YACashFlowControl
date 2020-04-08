<?php namespace App\Http\Controllers\Processes;

use App\AppAccount;
use App\AppAccountPeriod;
use App\AppOperation;
use App\AppBalanceAccount;
use App\AppBalanceReal;
use App\AppBalanceInSync;
use App\Http\Controllers\ManageDollarValue;
use CreditCardSummaries;
use DateInterval;
use DateTime;
use App\Helpers\Format;
use App\Helpers\DatetimeOperations;
use CRUDBooster;

class GenerateOperationsFromPlan {

    static public function compute_operations($data)
	{
		setlocale(LC_ALL, 'es_AR.utf8');
		$frequency_data = [
			1 => new DateInterval('P1W'), //Semanal
			2 => new DateInterval('P1M'), //Mensual
			3 => new DateInterval('P2M'), //Bimestral
			4 => new DateInterval('P3M'), //Trimestral
			5 => new DateInterval('P4M'), //Cuatrimestral
			6 => new DateInterval('P6M'), //Semestral
			7 => new DateInterval('P1Y'), //Anual
			8 => new DateInterval('P2Y'), //Bianual
		];
		$ordinal_numbers = [
			1 => 'Primera',
			2 => 'Segunda',
			3 => 'Tercera',
			4 => 'Cuarta',
			5 => 'Quinta'
		];

		//Get Holidays
		$holidays = \App\AuxHoliday::where('date', '>', $data->first_execution)->pluck('date')->all();

		$i = 0;
		$operations = [];

		$first_execution = new DateTime($data->first_execution);
		$operation_date = new DateTime($data->first_execution);

		while (true) {
			$installment = $i + 1;
			$operation['entry_id'] = $data->entry_id;
			$operation['account_id'] = $data->account_id;
			$operation['entry_type'] = $data->entry->entry_type;
			$operation['area_id'] = $data->entry->area_id;
			$operation['category_id'] = $data->entry->category_id;
			$operation['plan_id'] = $data->id;
			$operation['currency'] = $data->currency_plan;
			$operation['estimated_amount'] = $data->amount;

			//Estimated date based on the begining of the period
			$estimated_based_on = clone ($operation_date);

			//Estimated date based on the end of the period
			if ($data->estimated_based_on == 2) {
				$estimated_based_on->add($frequency_data[(int) $data->frequency_id]);
			}

			//Apply offset
			if ($data->estimated_offset != "") {
				$estimated_offset = explode('|', $data->estimated_offset);
				//Add or substract period
				if ($estimated_offset[0] == '+') {
					$method = 'add';
				} else {
					$method = 'sub';
				}
				//Apply the offset for correlated days
				if ($estimated_offset[2] == 0) {
					$estimated_based_on->$method(new DateInterval($estimated_offset[1]));
				} else {
					//Apply the days of offset just on working days (Only allowed for days periods)
					$days = (int) str_replace('P', '', str_replace('D', '', $estimated_offset[1]));
					$estimated_based_on = DatetimeOperations::moveWorkingDays($estimated_based_on, $method, $days);
				}
			}

			$operation['estimated_date'] = $estimated_based_on->format("Y-m-d H:i:s");
			$operation['settlement_date'] = $estimated_based_on->format('Ym');
			$operation['settlement_week'] = Format::get_week_of_month($estimated_based_on);


			//If the operation was in the past is marked as done (with all the required fields)
			/*if ($operation_date->format('Ymd') <= $now->format('Ymd')) {
				$operation['is_done'] = 1;
				$operation['operation_amount'] = $data->amount;
				$operation['operation_date'] = $operation_date->format("Y-m-d H:i:s");
				$operation['dollar_value'] = ManageDollarValue::get_value_of($operation_date->format('Y-m-d'));

				if ($data->currency == '$') {
					$operation['in_dollars'] = $operation['operation_amount'] / $operation['dollar_value'] * 100;
				}
			} else {
				$operation['is_done'] = 0;
			}*/
			$operation['is_done'] = 0;

			//For recursive plan
			if ($data->plan === -1) {
				//Stop the while after generate a year of operations
				if ($first_execution->format('Ym') + 100 <= $operation['settlement_date']) {
					break;
				}

				if ($data->frequency_id === 1) {

					$week_of_month = Format::get_week_of_month($operation_date);
					$operation['detail'] = strftime("{$ordinal_numbers[$week_of_month]} semana de %B de %Y", $operation_date->getTimestamp());
				} else {
					//Calculate the end of the period
					$toDate = clone $operation_date;
					$toDate->add($frequency_data[$data->frequency_id]);


					//Apply format to the detail
					if ($data->detail_format === 1) {

						$operation['detail'] =  strftime("Período desde el %e de %B de %Y", $operation_date->getTimestamp())
							. strftime(" hasta el %e de %B de %Y", $toDate->getTimestamp());
						//Cuota anualizada (Cuota NN/TT)
					} else if ($data->detail_format === 2) {
						
						switch ($data->frequency_id) {
							case 2; //12 installments for monthly 
								$installments = 12;
								break;
							case 3; //6 installments for bimonthly 
								$installments = 6;
								break;
							case 4; //4 installments for three months
								$installments = 4;
								break;
							case 5; //3 installments for a quarter
								$installments = 3;
								break;
							case 6; //2 installments for Semestral
								$installments = 2;
								break;
						}
						$operation['detail'] = "CUOTA {$installment}/{$installments} " . $operation_date->format('Y');
						//Rango mensual (MM AA - MM AA)
					} else {
						if ($data->frequency_id === 2) {
							$operation['detail'] = strtoupper(strftime('%B %Y', $operation_date->format('U')));
						} else {
							$operation['detail'] = strtoupper(strftime('%B %Y', $operation_date->format('U'))) . ' - ' . strtoupper(strftime('%B %Y', $toDate->format('U')));
						}
					}
				}
			} elseif ($data->plan === 1) {
				$operation['detail'] = 'Pago único';
			} else {
				$operation['detail'] = "CUOTA {$installment}/{$data->plan}";
			}


			$operation['created_by'] = CRUDBooster::myId();

			$operation['number'] = $installment;
			array_push($operations, $operation);
			unset($operation);
			$i++;

			//Stop when the operation number == the amount of operations 
			if ($data->plan == $i) {
				break;
			}
			if (241 == $i) {
				break;
			}

			$operation_date->add($frequency_data[$data->frequency_id]);
		}

		return $operations;
	}

}

