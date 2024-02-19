<?php
namespace common\modules\payment\components;

use Yii;
use yii\base\Component;
use yii\db\Query;
use yii\db\Schema;

use common\modules\payment\interfaces\IStateSaver;

/**
 * Class StateSaverDb
 * @package common\modules\payment\components
 */
class StateSaverDb extends Component implements IStateSaver
{
	
	/**
	 * @var string
	 */
    public $tableName;
    
    /**
     * @param string|int $id
     * @param array $data
     */
	/**
	 * @param $id
	 * @param $data
	 *
	 * @throws \yii\db\Exception
	 */
    public function set($id, $data) {
        $this->lazyCreateTable();

        Yii::$app->db->createCommand()->insert($this->tableName, [
            'key' => $id,
            'value' => $data,
        ])->execute();
    }

    /**
     * @param string|int $id
     * @return mixed|null
     */
    public function get($id) {
        $this->lazyCreateTable();

        return (new Query())->select('value')->from($this->tableName)->where([
            'key' => $id,
        ])->scalar() ?: null;
    }
	
	/**
	 * @throws \yii\db\Exception
	 */
    protected function lazyCreateTable() {
        if (Yii::$app->db->schema->getTableSchema($this->tableName, true) === null) {
            Yii::$app->db->createCommand()->createTable($this->tableName, [
                'id' => Schema::TYPE_PK,
                'key' => Schema::TYPE_STRING,
                'value' => Schema::TYPE_TEXT,
            ])->execute();
        }
    }

}