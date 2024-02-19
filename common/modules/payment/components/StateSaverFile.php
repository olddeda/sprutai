<?php
namespace common\modules\payment\components;

use yii\base\Component;

use common\modules\payment\interfaces\IStateSaver;

/**
 * Class StateSaverFile
 * @package common\modules\payment\components
 */
class StateSaverFile extends Component implements IStateSaver
{
	/**
	 * @var string
	 */
    public $savePath;
	
	/**
	 * @inheritdoc
	 */
    public function init() {
        parent::init();

        $this->savePath = $this->savePath ?: \Yii::$app->runtimePath;
        $this->savePath = rtrim($this->savePath, '/');
    }
	
	/**
	 * @param string|int $id
	 * @param array $data
	 */
	public function set($id, $data) {
		file_put_contents($this->savePath.'/'.md5($id).'.json', json_encode($data));
	}
	
	/**
	 * @param string|int $id
	 *
	 * @return mixed|null
	 */
	public function get($id) {
		$path = $this->savePath.'/'.md5($id).'.json';
		if (!file_exists($path)) return null;
		return json_decode(file_get_contents($path));
	}

}