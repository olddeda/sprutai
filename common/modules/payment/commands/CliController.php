<?php
namespace common\modules\payment\commands;

use Yii;
use yii\console\Controller;
use yii\console\ExitCode;
use yii\helpers\Console;
use yii\console\widgets\Table;

use common\modules\base\helpers\enum\ModuleType;

use common\modules\payment\models\Payment;
use common\modules\payment\models\PaymentType;
use common\modules\payment\models\PaymentWithdrawal;
use common\modules\payment\helpers\enum\Kind;
use common\modules\payment\helpers\enum\Status;

/**
 * Class CriController
 * @package common\modules\payment\commands
 */

class CliController extends Controller
{
	
	public $month = -1;
	
	public function options($actionID) {
		return ['month'];
	}
	
	public function optionAliases() {
		return ['m' => 'month'];
	}
	
	public function actionIndex() {
	
	}
	
	public function actionWithdrawals() {
		$lastMonth = strtotime($this->month.' month');
		$currentMonth =  strtotime(($this->month + 1).' month');
		$date = date('Y-m', $lastMonth);
		$tax = (date('Y', $lastMonth) > 2018) ? Payment::tax() : 0.0;
		
		echo PHP_EOL;
		echo $this->ansiFormat('Widhdrawals for date '.date('m-Y', $lastMonth), Console::FG_GREEN).PHP_EOL;
		echo $this->ansiFormat('Tax: '.$tax, Console::FG_GREEN).PHP_EOL;
		
		$paymentType = PaymentType::findByIdentifier('withdrawal', true);
		
		$query = Payment::find()->where('module_type = :module_type AND kind = :kind AND status = :status AND to_user_id IS NOT NULL AND DATE_FORMAT(FROM_UNIXTIME(date_at), \'%Y-%m\') = :date', [
			':module_type' => ModuleType::CONTENT,
			':kind' => Kind::ACCRUAL,
			':status' => Status::PAID,
			':date' => $date
		])->orderBy('price DESC');
		
		$sum = $query->sum('price');
		
		foreach ($query->all() as $row) {
			$data[$row->to_user_id][] = [
				'payment_id' => $row->id,
				'price' => $row->price,
				'user_fio' => $row->toUser ? $row->toUser->getAuthorName(true) : $row->to_user_id,
			];
		}
		
		$idx = 0;
		$tableRows = [];
		
		$sumTotal = 0;
		foreach ($data as $userId => $rows) {
			
			if (Payment::find()->where('kind = :kind AND user_id = :user_id AND DATE_FORMAT(FROM_UNIXTIME(date_at), \'%Y-%m\') = :date', [
				':kind' => Kind::WITHDRAWAL,
				':user_id' => $userId,
				':date' => date('Y-m', $currentMonth),
			])->exists())
				continue;
			
			$userSum = array_sum(array_column($rows, 'price'));
			$sumTotal += $userSum;
			
			$transaction = Yii::$app->db->beginTransaction();
			try {
				$model = new Payment();
				$model->kind = Kind::WITHDRAWAL;
				$model->payment_type_id = $paymentType->id;
				$model->user_id = $userId;
				$model->price = $userSum;
				$model->tax = $tax;
				$model->descr = Yii::t('payment', 'withdrawal_author_month', ['date' => Yii::$app->formatter->asDate($lastMonth, 'LLLL yyyy')]);
				$model->date_at = $currentMonth;
				$model->status = Status::WAIT;
				$model->save();
				
				foreach ($rows as $row) {
					$modelWithdrawal = new PaymentWithdrawal();
					$modelWithdrawal->payment_id = $model->id;
					$modelWithdrawal->payment_source_id = $row['payment_id'];
					$modelWithdrawal->save();
					$modelWithdrawal->link('payment', $model);
				}
				
				$transaction->commit();
				
				$tableRows[] = [
					0,
					$row['user_fio'],
					count($rows),
					$tax.'%',
					Yii::$app->formatter->asCurrency($userSum),
					Yii::$app->formatter->asCurrency($userSum * ((100 - $tax) / 100)),
				];
				
				$idx++;
				
			} catch (\Exception $e) {
				$transaction->rollBack();
				throw $e;
			} catch (\Throwable $e) {
				$transaction->rollBack();
				throw $e;
			}
		}
		
		$sumTax = $sumTotal * ((100 - $tax) / 100);
		$sumClear = $sumTotal - $sumTax;
		
		if (count($tableRows)) {
			usort($tableRows, function ($item1, $item2) {
				return $item2[5] <=> $item1[5];
			});
			
			$idx = 0;
			foreach ($tableRows as $key => $row) {
				$tableRows[$key][0] = ++$idx;
			}
			
			$tableRows[] = [
				'Total',
				'',
				'',
				Yii::$app->formatter->asCurrency($sumClear),
				Yii::$app->formatter->asCurrency($sumTotal),
				Yii::$app->formatter->asCurrency($sumTax)
			];
			
			echo Table::widget([
				'headers' => ['№', 'User name', 'Count', 'Tax', 'Total', 'Total with tax'],
				'rows' => $tableRows,
			]);
		}
		else {
			echo $this->ansiFormat('Not found', Console::FG_RED).PHP_EOL;
		}
		
		echo PHP_EOL;
		
		return ExitCode::OK;
	}
	
	public function actionReset() {
		$time = strtotime($this->month.' month');
		$date = date('Y-m', $time);

		Payment::deleteAll('kind = :kind AND DATE_FORMAT(FROM_UNIXTIME(date_at), \'%Y-%m\') = :date', [
			':kind' => Kind::WITHDRAWAL,
			':date' => $date,
		]);
	}
}