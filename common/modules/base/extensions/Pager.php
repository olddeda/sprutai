<?php

namespace common\modules\base\extensions;

use Yii;
use yii\base\Widget;
use yii\helpers\Html;
use yii\base\InvalidConfigException;

/**
 * Class Pager
 *
 * <?php
 * echo \common\modules\base\extensions\Pager::widget([
 * 	'tableName' => $model->tableName(), // table name
 * 	'id' => $model->id, // current primary key
 * 	'primaryKey' => 'id', // name of primary key column
 * 	'title' => 'title', // name of title column
 * 	'path' => 'story/view', // path for link
 * 	'additionalСondition' => 'published = 1', // additional SQL-condition
 * 	'cacheTime' => 3600, // time for cache results
 * ]);
?>
 */
class Pager extends Widget {

	/**
	 * Имя таблицы
	 * @var string
	 */
	public $tableName;

	/**
	 * Значение текущего первичного ключа
	 * @var int
	 */
	public $id;

	/**
	 * Имя столбца с первичным ключом
	 * @var string
	 */
	public $primaryKey = 'id';

	/**
	 * Имя столбца с заголовком
	 * @var string
	 */
	public $title = 'title';

	/**
	 * Время кеширования результатов в секундах
	 * @var int
	 */
	public $cacheTime = 3600;

	/**
	 * Дополнительное SQL-условие
	 * @var string
	 */
	public $additionalСondition = '1=1';

	/**
	 * Путь для формирования ссылки
	 * @var string
	 */
	public $path = 'action/view';

	/**
	 * @var array
	 */
	protected $closetLinks;

	public function init(){
		parent::init();
		if($this->tableName == null)
			throw new InvalidConfigException('Table name is not configured!');
		$this->closetLinks = Yii::$app->cache->get($this->tableName.'closestLinks'.$this->id);
		if($this->closetLinks === false) {
			$nextQuery = Yii::$app->db->createCommand("SELECT {$this->primaryKey},{$this->title} FROM {$this->tableName} WHERE {$this->primaryKey} > {$this->id} AND {$this->additionalСondition} LIMIT 1");
			if(($next = $nextQuery->queryOne()) == false) {
				$next = Yii::$app->db->createCommand("SELECT {$this->primaryKey},{$this->title} FROM {$this->tableName} WHERE {$this->additionalСondition} ORDER BY {$this->primaryKey} ASC LIMIT 1")->queryOne();
			}
			$prevQuery = Yii::$app->db->createCommand("SELECT {$this->primaryKey},{$this->title} FROM {$this->tableName} WHERE {$this->primaryKey} < {$this->id} AND {$this->additionalСondition} ORDER BY {$this->primaryKey} DESC LIMIT 1");
			if(($prev = $prevQuery->queryOne()) == false) {
				$prev = Yii::$app->db->createCommand("SELECT {$this->primaryKey},{$this->title} FROM {$this->tableName} WHERE {$this->additionalСondition} ORDER BY {$this->primaryKey} DESC LIMIT 1")->queryOne();
			}
			$this->closetLinks = ['next'=>$next, 'prev'=>$prev];
			Yii::$app->cache->set($this->tableName.'closestLinks'.$this->id, $this->closetLinks, $this->cacheTime);
		}
	}

	public function run(){
		return "
        <nav>
            <ul class='pager'>
                <li class='previous'>".Html::a('&larr; '.$this->closetLinks['prev'][$this->title], [$this->path, 'id' => $this->closetLinks['prev'][$this->primaryKey]], ['class'=>'pull-left', 'rel'=>'prev'])."</li>
                <li class='next'>".Html::a($this->closetLinks['next'][$this->title].' &rarr;', [$this->path, 'id' => $this->closetLinks['next'][$this->primaryKey]], ['class'=>'pull-right', 'rel'=>'next'])."</li>
            </ul>
        </nav>";
	}
}