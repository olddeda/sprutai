<?php
namespace api\models\user;

use yii\db\ActiveQuery;

use common\modules\base\helpers\enum\ModuleType;
use common\modules\user\models\UserActivity as BaseModel;

use api\models\content\ContentActivity;
use api\models\comment\CommentActivity;
use api\models\catalog\CatalogItemActivity;
use api\models\achievement\AchievementActivity;
use api\models\achievement\AchievementUserActivity;

/**
 * Class UserActivity
 * @package api\models\user
 */
class UserActivity extends BaseModel
{

    /**
     * @inheritdoc
     */
    public function fields() {
        return [
            'id',
            'type_id' => function($data) {
                return $data->type;
            },
            'type' => function ($data) {
                return $data->getTypeName();
            },
            'module_type',
            'module_id',
            'parent_module_type',
            'parent_module_id',
            'user_id',
            'from_user_id',
            'date_at',
            'module' => function($data) {
                if ($data->module_type == ModuleType::USER) {
                    return $data->userModule;
                }
                if ($data->module_type == ModuleType::CONTENT) {
                    return $data->content;
                }
                if ($data->module_type == ModuleType::COMMENT) {
                    return $data->comment;
                }
                if ($data->module_type == ModuleType::CATALOG_ITEM) {
                    return $data->catalogItem;
                }
                if ($data->module_type == ModuleType::ACHIEVEMENT_USER) {
                    return $data->achievementUser;
                }
                return null;
            },
            'parent_module' => function($data) {
                if ($data->parent_module_type == ModuleType::CONTENT) {
                    return $data->parentContent;
                }
                if ($data->parent_module_type == ModuleType::COMMENT) {
                    return $data->parentComment;
                }
                if ($data->parent_module_type == ModuleType::CATALOG_ITEM) {
                    return $data->parentCatalogItem;
                }
                if ($data->parent_module_type == ModuleType::ACHIEVEMENT) {
                    return $data->parentAchievement;
                }
                return null;
            },
            'user',
            'user_from' => function ($data) {
                return $data->userFrom;
            }
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getUser() {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getUserFrom() {
        return $this->hasOne(User::class, ['id' => 'from_user_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getUserModule() {
        return $this->hasOne(User::class, ['id' => 'module_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getContent() {
        return $this->hasOne(ContentActivity::class, ['id' => 'module_id'])->where([]);
    }

    /**
     * @return ActiveQuery
     */
    public function getParentContent() {
        return $this->hasOne(ContentActivity::class, ['id' => 'parent_module_id'])->where([]);
    }

    /**
     * @return ActiveQuery
     */
    public function getComment() {
        return $this->hasOne(CommentActivity::class, ['id' => 'module_id'])->where([]);
    }

    /**
     * @return ActiveQuery
     */
    public function getParentComment() {
        return $this->hasOne(CommentActivity::class, ['id' => 'parent_module_id'])->where([]);
    }

    /**
     * @return ActiveQuery
     */
    public function getCatalogItem() {
        return $this->hasOne(CatalogItemActivity::class, ['id' => 'module_id'])->where([]);
    }

    /**
     * @return ActiveQuery
     */
    public function getParentCatalogItem() {
        return $this->hasOne(CatalogItemActivity::class, ['id' => 'parent_module_id'])->where([]);
    }

    /**
     * @return ActiveQuery
     */
    public function getAchievementUser() {
        return $this->hasOne(AchievementUserActivity::class, ['id' => 'module_id'])->where([]);
    }

    /**
     * @return ActiveQuery
     */
    public function getParentAchievement() {
        return $this->hasOne(AchievementActivity::class, ['id' => 'parent_module_id'])->where([]);
    }
}