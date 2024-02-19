<?php
namespace common\modules\telegram\models;

use Yii;
use yii\db\Query;
use yii\caching\DbDependency;
use yii\helpers\ArrayHelper;

use common\modules\base\helpers\enum\Status;
use common\modules\base\components\Debug;
use common\modules\base\components\ActiveRecord;
use common\modules\base\helpers\enum\ModuleType;

use common\modules\media\behaviors\MediaBehavior;
use common\modules\media\helpers\enum\Type as MediaType;

use common\modules\tag\models\Tag;
use common\modules\tag\models\TagModule;

use common\modules\telegram\models\query\TelegramChatQuery;

/**
 * This is the model class for table "{{%telegram_chat}}".
 *
 * @property integer $id
 * @property string $title
 * @property string $identifier
 * @property string $username
 * @property string $description
 * @property integer $members_count
 * @property boolean $notify_content
 * @property boolean $notify_payment
 * @property boolean $is_partner
 * @property boolean $is_channel
 * @property boolean $is_spam_protect
 * @property integer $status
 * @property integer $created_by
 * @property integer $updated_by
 * @property integer $created_at
 * @property integer $updated_at
 *
 * Defined relations:
 * @property \common\modules\tag\models\Tag[] $tags
 */
class TelegramChat extends ActiveRecord
{
	
	/** @var array */
	private $_tags_ids;
	
	/** @var array */
	private $_tags_ids_old;
	
	/**
	 * Get module type
	 * @return int
	 */
	public function getModuleType() {
		return ModuleType::TELEGRAM_CHAT;
	}
	
    /**
     * @inheritdoc
     */
    public static function tableName() {
		return '{{%telegram_chat}}';
	}
	
	
	/**
	 * @inheritdoc
	 */
	public function behaviors() {
		return ArrayHelper::merge(parent::behaviors(), [
			[
				'class' => MediaBehavior::class,
				'attribute' => 'logo',
				'type' => MediaType::IMAGE,
			],
		]);
	}

    /**
     * @inheritdoc
     */
    public function rules()  {
        return [
            [['members_count', 'notify_content', 'notify_payment', 'is_partner', 'is_channel', 'is_spam_protect', 'status', 'created_by', 'updated_by', 'created_at', 'updated_at'], 'integer'],
			[['title', 'identifier', 'username'], 'string', 'max' => 255],
            [['description'], 'string', 'max' => 1000],
            [['title', 'identifier', 'username'], 'required'],
			[['tags_ids'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => Yii::t('telegram-chat', 'field_id'),
            'title' => Yii::t('telegram-chat', 'field_title'),
            'identifier' => Yii::t('telegram-chat', 'field_identifier'),
			'username' => Yii::t('telegram-chat', 'field_username'),
            'description' => Yii::t('telegram-chat', 'field_description'),
            'members_count' => Yii::t('telegram-chat', 'field_members_count'),
			'notify_content' => Yii::t('telegram-chat', 'field_notify_content'),
            'notify_payment' => Yii::t('telegram-chat', 'field_notify_payment'),
			'is_partner' => Yii::t('telegram-chat', 'field_is_partner'),
            'is_channel' => Yii::t('telegram-chat', 'field_is_channel'),
            'is_spam_protect' => Yii::t('telegram-chat', 'field_is_spam_protect'),
            'members_count' => Yii::t('telegram-chat', 'field_members_count'),
            'status' => Yii::t('telegram-chat', 'field_status'),
            'created_at' => Yii::t('telegram-chat', 'field_created_at'),
            'updated_at' => Yii::t('telegram-chat', 'field_updated_at'),
			'tags_ids' => Yii::t('telegram-chat', 'field_tags_ids'),
        ];
    }
	
	/**
	 * @inheritdoc
	 * @return \common\modules\telegram\models\query\TelegramChatQuery the active query used by this AR class.
	 */
	public static function find() {
		return new TelegramChatQuery(get_called_class());
	}
	
	/**
	 * @return \common\modules\tag\models\query\TagModuleQuery
	 */
	public function getTagModule() {
		return $this->hasMany(TagModule::class, ['module_id' => 'id'])->onCondition([
			TagModule::tableName().'.module_type' => self::moduleType(),
		])->where([]);
	}
	
	/**
	 * @return \common\modules\tag\models\query\TagQuery
	 */
	public function getTags() {
		return $this->hasMany(Tag::class, ['id' => 'tag_id'])->via('tagModule')->where([]);
	}
	
	/**
	 * Get identifiers array
	 * @param array $conditions
	 *
	 * @return array
	 */
	static public function getIdentifiers(array $conditions = []) {
		$dependency = new DbDependency();
		$dependency->sql = 'SELECT MAX(updated_at) FROM '.self::tableName();
		
		$ids = [];
		$query = (new Query(null, $dependency))
			->cache()
			->select('identifier')
			->from(self::tableName())
			->where(['status' => Status::ENABLED]);
		if (count($conditions))
			$query->andWhere($conditions);
		foreach ($query->batch() as $rows) {
			$ids = ArrayHelper::merge($ids, (ArrayHelper::getColumn($rows, 'identifier')));
		}
		
		return $ids;
	}
	
	/**
	 * Get identifiers array for content
	 *
	 * @return array
	 */
	static public function getIdentifiersContent($tagsIds = []) {
		if (is_array($tagsIds) && count($tagsIds)) {
			return ArrayHelper::getColumn(self::find()->joinWith(['tags'])
				->where([
					self::tableName().'.status' => Status::ENABLED,
					self::tableName().'.notify_content' => true,
				])
				->andWhere([
					'in', Tag::tableName().'.id', $tagsIds,
				])->all(),
			'identifier');
		}
		
		return self::getIdentifiers(['notify_content' => true]);
	}
	
	/**
	 * Get identifiers array for content
	 *
	 * @return array
	 */
	static public function getIdentifiersPayment() {
		return self::getIdentifiers(['notify_payment' => true]);
	}
	
	/**
	 * Get tags ids
	 * @return array
	 */
	public function getTags_ids() {
		if (is_null($this->_tags_ids)) {
			$this->_tags_ids = [];
			$tags = $this->tags;
			if ($tags) {
				foreach ($tags as $item)
					$this->_tags_ids[] = $item->id;
			}
		}
		return $this->_tags_ids;
	}
	
	/**
	 * Set tags ids
	 * @param $val
	 */
	public function setTags_ids($val) {
		$this->_tags_ids = (is_null($val)) ? [] : $val;
	}
	
	/**
	 * Get tags values
	 * @param bool $asArray
	 * @param string $glue
	 *
	 * @return array|null|string
	 */
	public function getTagsValues($asArray = false, $glue = ', ') {
		$tmp = $this->tags;
		if (is_array($tmp)) {
			$values = ArrayHelper::getColumn($tmp, 'title');
			return ($asArray) ? $values : implode($glue, $values);
		}
		return ($asArray) ? [] : null;
	}
	
	/**
	 * @inheritdoc
	 */
	public function afterFind() {
		parent::afterFind();
		
		// Set tags
		if ($this->isRelationPopulated('tags'))
			$this->_tags_ids_old = $this->getTags_ids();
	}
	
	/**
	 * @inheritdoc
	 */
	public function afterSave($insert, $changedAttributes) {
		parent::afterSave($insert, $changedAttributes);
		
		// Update tags links
		TagModule::updateLinks($this->_tags_ids_old, $this->_tags_ids, self::moduleType(), $this->id);
	}
}
