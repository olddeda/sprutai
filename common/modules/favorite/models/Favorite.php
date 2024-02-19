<?php
namespace common\modules\favorite\models;

use Yii;
use yii\db\ActiveQuery;
use common\modules\base\components\Debug;

use common\modules\base\components\ActiveRecord;
use common\modules\base\helpers\enum\ModuleType;

use common\modules\catalog\models\CatalogItem;
use common\modules\catalog\models\CatalogItemStat;

use common\modules\user\models\User;

use common\modules\achievement\models\AchievementUser;
use common\modules\achievement\models\Achievement;
use common\modules\achievement\helpers\enum\Type AS AchievementType;

use common\modules\favorite\models\query\FavoriteQuery;

/**
 * This is the model class for table "{{%favorite}}".
 *
 * @property integer $id
 * @property integer $group_id
 * @property integer $module_type
 * @property integer $module_id
 * @property integer $user_id
 * @property integer $created_at
 * @property integer $updated_at
 *
 * Defined relations:
 * @property FavoriteGroup $group
 * @property User $user
 */
class Favorite extends ActiveRecord
{
    const GROUP_ID = 4;

	/**
	 * @inheritdoc
	 */
	public static function tableName() {
		return '{{%favorite}}';
	}

	/**
	 * @inheritdoc
	 */
	public function rules() {
		return [
			[['group_id', 'module_type', 'module_id', 'user_id'], 'required'],
			[['group_id', 'module_type', 'module_id', 'user_id', 'created_at', 'updated_at'], 'integer'],
		];
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels() {
		return [
			'id' => Yii::t('favorite', 'field_id'),
            'group_id' => Yii::t('favorite', 'field_group_id'),
			'module_type' => Yii::t('favorite', 'field_module_type'),
			'module_id' => Yii::t('favorite', 'field_module_id'),
			'user_id' => Yii::t('favorite', 'field_user_id'),
			'created_at' => Yii::t('favorite', 'field_created_at'),
			'updated_at' => Yii::t('favorite', 'field_updated_at'),
		];
	}

    /**
     * @inheritdoc
     * @return FavoriteQuery the active query used by this AR class.
     */
    public static function find() {
        return new FavoriteQuery(get_called_class());
    }

    /**
     * @return ActiveQuery
     */
    public function getGroup() {
        return $this->hasOne(FavoriteGroup::class, ['id' => 'group_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getUser() {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    /**
     * Update catalog item stat
     */
    public function updateLinks() {
        if ($this->module_type == ModuleType::CATALOG_ITEM) {

            /** @var CatalogItem $catalogItem */
            $catalogItem = CatalogItem::find()->where(['id' => $this->module_id])->one();
            if ($catalogItem) {
                CatalogItemStat::updateLinks($catalogItem);

                if ($this->group_id == self::GROUP_ID) {
                    $levels = Achievement::find()->select('level')->andWhere(['type' => AchievementType::OWNER])->column();
                    $level = $catalogItem->stat->favorite_have;
                    if (in_array($level, $levels)) {
                        $achievement = Achievement::find()->andWhere([
                            'type' => AchievementType::OWNER,
                            'level' => $level,
                        ])->one();
                        if ($achievement) {
                            if (!AchievementUser::find()->joinWith(['achievement'])->where([
                                AchievementUser::tableName().'.achievement_id' => $achievement->id,
                                AchievementUser::tableName().'.user_id' => Yii::$app->user->id,
                            ])->exists()) {
                                $achievementUser = new AchievementUser();
                                $achievementUser->achievement_id = $achievement->id;
                                $achievementUser->user_id = Yii::$app->user->id;
                                if ($achievementUser->save()) {
                                    $message = Yii::t('notification', 'catalog_item_owner_new_level', [
                                        'url' => 'https://v2.sprut.ai/catalog/item/'.$catalogItem->seo->slugify,
                                        'title' => $catalogItem->title,
                                        'count' => Yii::t('notification', 'catalog_item_owner_new_level_count', ['n' => $level]),
                                    ]);
                                    //Yii::$app->notification->queueTelegramIds([-1001082506583], $message);
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * @param bool $insert
     * @param array $changedAttributes
     */
    public function afterSave($insert, $changedAttributes) {
        parent::afterSave($insert, $changedAttributes);

        $this->updateLinks();
    }

    /**
     * @inheritDoc
     */
    public function afterDelete() {
        parent::afterDelete();

        $this->updateLinks();
    }
}
