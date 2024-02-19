<?php
namespace common\modules\content\migrations;

use Yii;
use yii\db\Command;
use yii\db\Migration;
use yii\helpers\Console;

use common\modules\content\helpers\enum\Type;
use common\modules\content\helpers\enum\Status;
use common\modules\content\models\Content;

use common\modules\vote\models\Vote;
use common\modules\vote\models\VoteAggregate;


/**
 * Migrate votes
 */
class m190305_195800_migrate_content_votes extends Migration
{
	/**
	 * @inheritdoc
	 */
	public function up() {
		
		$models = Content::find()
			->where(['status' => Status::ENABLED])
			->andWhere(['in', 'type', [Type::ARTICLE, Type::BLOG, Type::NEWS, Type::PLUGIN, Type::PROJECT]])
			->orderBy('date_at ASC')
			->all();
		
		$count = count($models);
		echo 'Found: '.$count.PHP_EOL;
		
		$i = 0;
		foreach ($models as $model) {
			echo ++$i.'/'.$count.': '.$model->title.' ОК'.PHP_EOL;
			$this->_migrateVotes($model, $model->type, Type::NONE);
		}
		
		return true;
	}

	/**
	 * @inheritdoc
	 */
	public function down() {
		
		$models = Content::find()
			->where(['status' => Status::ENABLED])
			->andWhere(['in', 'type', [Type::ARTICLE, Type::BLOG, Type::NEWS, Type::PLUGIN, Type::PROJECT]])
			->orderBy('date_at ASC')
			->all();
		
		$count = count($models);
		echo 'Found: '.$count.PHP_EOL;
		
		$i = 0;
		foreach ($models as $model) {
			echo ++$i.'/'.$count.': '.$model->title.' ОК'.PHP_EOL;
			$this->_migrateVotes($model, Type::NONE, $model->type);
		}
		
		return true;
	}
	
	/**
	 * Migrate votes from old type to new type
	 *
	 * @param Content $model
	 * @param int $oldType
	 * @param int $newType
	 *
	 * @throws \yii\base\InvalidConfigException
	 */
	private function _migrateVotes(Content $model, int $oldType, int $newType) {
		
		/** @var \common\modules\vote\Module $module */
		$module = Yii::$app->getModule('vote');
		
		$types = [];
		foreach ([$oldType, $newType] as $type) {
			foreach ($module->getEntitiesForClass($this->_getTypesClasses($type)) as $entity) {
				$settings = $module->getSettingsForEntity($entity);
				$types[$type][$settings['type']] = $entity;
			}
		}
		
		foreach ($types[$oldType] as $type => $entity) {
			if (isset($types[$newType][$type])) {
				$types['change'][$entity] = $types[$newType][$type];
				$types['changeEncoded'][$module->encodeEntity($entity)] = $module->encodeEntity($types[$newType][$type]);
			}
		}
		
		if (isset($types['changeEncoded'])) {
			foreach ($types['changeEncoded'] as $fromEntity => $toEntity) {
				Vote::updateAll([
					'entity' => $toEntity,
				], 'entity = :entity AND entity_id = :entity_id', [
					':entity' => $fromEntity,
					':entity_id' => $model->id,
				]);
				
				VoteAggregate::updateAll([
					'entity' => $toEntity,
				], 'entity = :entity AND entity_id = :entity_id', [
					':entity' => $fromEntity,
					':entity_id' => $model->id,
				]);
			}
		}
	}
	
	/**
	 * Get classes list or one by type
	 * @param null $type
	 *
	 * @return array|mixed|null
	 */
	private function _getTypesClasses($type = null) {
		$classes = [
			Type::NONE => 'common\modules\content\models\Content',
			Type::ARTICLE => 'common\modules\content\models\Article',
			Type::NEWS => 'common\modules\content\models\News',
			Type::BLOG => 'common\modules\content\models\Blog',
			Type::PROJECT => 'common\modules\project\models\Project',
			Type::PLUGIN => 'common\modules\plugin\models\Plugin',
		];
		
		if (!is_null($type))
			return (isset($classes[$type])) ? $classes[$type] : null;
		
		return $classes;
	}
}
