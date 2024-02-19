<?php
namespace api\modules\v1\controllers\dashboard;

use Yii;
use yii\db\Query;
use yii\db\Expression;
use yii\helpers\ArrayHelper;

use DateTime;

use common\modules\base\components\Controller;
use common\modules\base\helpers\enum\ModuleType;

use api\models\comment\Comment;
use api\models\favorite\Favorite;

/**
 * Class CatalogController
 * @package api\modules\v1\controllers\dashboard
 */
class CatalogController extends Controller
{
	public function behaviors() {
		return [];
	}
	
    public function actionStatReview() {
        return [
            'today' => $this->_reviewsToday(),
            'week' => $this->_reviewsWeek(),
            'month' => $this->_reviewsMonth(),
            'year' => $this->_reviewsYear(),
        ];
    }

	public function actionStatOwner() {
	    return [
            'today' => $this->_ownersToday(),
            'week' => $this->_ownersWeek(),
            'month' => $this->_ownersMonth(),
            'year' => $this->_ownersYear(),
        ];
    }

    private function _reviewsToday() {
        $query = (new Query())
            ->select([
                'HOUR(FROM_UNIXTIME(created_at)) as x',
                'COUNT(*) AS y',
            ])
            ->from(Comment::tableName())
            ->where([
                'DATE(FROM_UNIXTIME(created_at))' => new Expression('CURDATE()'),
            ])
            ->andWhere([
                'entity' => 'de2463ae',
                'status' => 1
            ])
            ->andWhere('(parent_id = 0 OR parent_id IS NULL)')
            ->groupBy('HOUR(FROM_UNIXTIME(created_at))')
            ->orderBy('created_at')
        ;
        $rows = $query->all();
        $found = [];
        foreach ($rows as $t) {
            $found[$t['x']] = $t['y'];
        }

        $tmp = [];
        for ($h = 0; $h <= 23; $h++) {
            $count = ArrayHelper::getValue($found, $h, 0);
            $tmp[] = [
                'x' => $h,
                'y' => (int)$count,
            ];
        }
        return $tmp;
    }

    private function _reviewsWeek() {
        $query = (new Query())
            ->select([
                'FROM_UNIXTIME(created_at) AS x',
                'COUNT(*) AS y',
            ])
            ->from(Comment::tableName())
            ->where([
                '>= ', 'DATE(FROM_UNIXTIME(created_at))', new Expression('(NOW() - INTERVAL 7 DAY)'),
            ])
            ->andWhere([
                'entity' => 'de2463ae',
                'status' => 1
            ])
            ->andWhere('(parent_id = 0 OR parent_id IS NULL)')
            ->groupBy('DATE(FROM_UNIXTIME(created_at))')
            ->orderBy('created_at')
        ;
        $rows = $query->all();
        $found = [];
        foreach ($rows as $t) {
            $x = Yii::$app->formatter->asDate(strtotime($t['x']), 'php:d F');
            $found[$x] = $t['y'];
        }

        $tmp = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = new DateTime();
            $date->modify('-'.$i.' day');
            $x = Yii::$app->formatter->asDate($date->getTimestamp(), 'php:d F');
            $count = ArrayHelper::getValue($found, $x, 0);
            $tmp[] = [
                'x' => $x,
                'y' => (int)$count,
            ];
        }
        return $tmp;
    }

    private function _reviewsMonth() {
        $query = (new Query())
            ->select([
                'FROM_UNIXTIME(created_at) AS x',
                'COUNT(*) AS y',
            ])
            ->from(Comment::tableName())
            ->where([
                '>= ', 'DATE(FROM_UNIXTIME(created_at))', new Expression('(NOW() - INTERVAL 1 MONTH)'),
            ])
            ->andWhere([
                'entity' => 'de2463ae',
                'status' => 1
            ])
            ->andWhere('(parent_id = 0 OR parent_id IS NULL)')
            ->groupBy('DATE(FROM_UNIXTIME(created_at))')
            ->orderBy('created_at')
        ;
        $rows = $query->all();
        $found = [];
        foreach ($rows as $t) {
            $x = Yii::$app->formatter->asDate(strtotime($t['x']), 'php:d F');
            $found[$x] = $t['y'];
        }

        $tmp = [];
        for ($i = 31; $i >= 0; $i--) {
            $date = new DateTime();
            $date->modify('-'.$i.' day');
            $x = Yii::$app->formatter->asDate($date->getTimestamp(), 'php:d F');
            $count = ArrayHelper::getValue($found, $x, 0);
            $tmp[] = [
                'x' => $x,
                'y' => (int)$count,
            ];
        }
        return $tmp;
    }

    private function _reviewsYear() {
        $query = (new Query())
            ->select([
                'FROM_UNIXTIME(created_at) AS x',
                'COUNT(*) AS y',
            ])
            ->from(Comment::tableName())
            ->where([
                '>= ', 'DATE(FROM_UNIXTIME(created_at))', new Expression('(NOW() - INTERVAL 1 YEAR)'),
            ])
            ->andWhere([
                'entity' => 'de2463ae',
                'status' => 1
            ])
            ->andWhere('(parent_id = 0 OR parent_id IS NULL)')
            ->groupBy('MONTH(FROM_UNIXTIME(created_at))')
            ->orderBy('created_at')
        ;

        $rows = $query->all();
        $found = [];
        foreach ($rows as $t) {
            $x = Yii::$app->formatter->asDate(strtotime($t['x']), 'MMM Y');
            $found[$x] = $t['y'];
        }

        $tmp = [];
        for ($i = 11; $i >= 0; $i--) {
            $date = new DateTime();
            $date->modify('-'.$i.' month');
            $x = Yii::$app->formatter->asDate($date->getTimestamp(), 'MMM Y');
            $count = ArrayHelper::getValue($found, $x, 0);
            $tmp[] = [
                'x' => $x,
                'y' => (int)$count,
            ];
        }
        return $tmp;
    }

    private function _ownersToday() {
        $query = (new Query())
            ->select([
                'HOUR(FROM_UNIXTIME(created_at)) as x',
                'COUNT(*) AS y',
            ])
            ->from(Favorite::tableName())
            ->where([
                'DATE(FROM_UNIXTIME(created_at))' => new Expression('CURDATE()'),
            ])
            ->andWhere([
                'module_type' => ModuleType::CATALOG_ITEM,
                'group_id' => 4,
            ])
            ->groupBy('HOUR(FROM_UNIXTIME(created_at))')
            ->orderBy('created_at')
        ;
        $rows = $query->all();
        $found = [];
        foreach ($rows as $t) {
            $found[$t['x']] = $t['y'];
        }

        $tmp = [];
        for ($h = 0; $h <= 23; $h++) {
            $count = ArrayHelper::getValue($found, $h, 0);
            $tmp[] = [
                'x' => $h,
                'y' => (int)$count,
            ];
        }
        return $tmp;
    }

    private function _ownersWeek() {
        $query = (new Query())
            ->select([
                'FROM_UNIXTIME(created_at) AS x',
                'COUNT(*) AS y',
            ])
            ->from(Favorite::tableName())
            ->where([
                '>= ', 'DATE(FROM_UNIXTIME(created_at))', new Expression('(NOW() - INTERVAL 7 DAY)'),
            ])
            ->andWhere([
                'module_type' => ModuleType::CATALOG_ITEM,
                'group_id' => 4,
            ])
            ->groupBy('DATE(FROM_UNIXTIME(created_at))')
            ->orderBy('created_at')
        ;
        $rows = $query->all();
        $found = [];
        foreach ($rows as $t) {
            $x = Yii::$app->formatter->asDate(strtotime($t['x']), 'php:d F');
            $found[$x] = $t['y'];
        }

        $tmp = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = new DateTime();
            $date->modify('-'.$i.' day');
            $x = Yii::$app->formatter->asDate($date->getTimestamp(), 'php:d F');
            $count = ArrayHelper::getValue($found, $x, 0);
            $tmp[] = [
                'x' => $x,
                'y' => (int)$count,
            ];
        }
        return $tmp;
    }

    private function _ownersMonth() {
        $query = (new Query())
            ->select([
                'FROM_UNIXTIME(created_at) AS x',
                'COUNT(*) AS y',
            ])
            ->from(Favorite::tableName())
            ->where([
                '>= ', 'DATE(FROM_UNIXTIME(created_at))', new Expression('(NOW() - INTERVAL 1 MONTH)'),
            ])
            ->andWhere([
                'module_type' => ModuleType::CATALOG_ITEM,
                'group_id' => 4,
            ])
            ->groupBy('DATE(FROM_UNIXTIME(created_at))')
            ->orderBy('created_at')
        ;
        $rows = $query->all();
        $found = [];
        foreach ($rows as $t) {
            $x = Yii::$app->formatter->asDate(strtotime($t['x']), 'php:d F');
            $found[$x] = $t['y'];
        }

        $tmp = [];
        for ($i = 31; $i >= 0; $i--) {
            $date = new DateTime();
            $date->modify('-'.$i.' day');
            $x = Yii::$app->formatter->asDate($date->getTimestamp(), 'php:d F');
            $count = ArrayHelper::getValue($found, $x, 0);
            $tmp[] = [
                'x' => $x,
                'y' => (int)$count,
            ];
        }
        return $tmp;
    }

    private function _ownersYear() {
        $query = (new Query())
            ->select([
                'FROM_UNIXTIME(created_at) AS x',
                'COUNT(*) AS y',
            ])
            ->from(Favorite::tableName())
            ->where([
                '>= ', 'DATE(FROM_UNIXTIME(created_at))', new Expression('(NOW() - INTERVAL 1 YEAR)'),
            ])
            ->andWhere([
                'module_type' => ModuleType::CATALOG_ITEM,
                'group_id' => 4,
            ])
            ->groupBy(new Expression('DATE_FORMAT(FROM_UNIXTIME(created_at), "%Y-%m")'))
            ->orderBy('created_at')
        ;

        $rows = $query->all();
        $found = [];
        foreach ($rows as $t) {
            $x = Yii::$app->formatter->asDate(strtotime($t['x']), 'php:m-Y');
            $found[$x] = $t['y'];
        }

        $tmp = [];
        for ($i = 11; $i >= 0; $i--) {
            $date = new DateTime();
            $date->modify('-'.$i.' month');
            $x = Yii::$app->formatter->asDate($date->getTimestamp(), 'php:m-Y');
            $count = ArrayHelper::getValue($found, $x, 0);
            $tmp[] = [
                'x' => $x,
                'y' => (int)$count,
            ];
        }
        return $tmp;
    }
}
