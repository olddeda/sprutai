<?php

namespace common\modules\vote\models;

use Yii;
use common\modules\vote\Module;
use common\modules\vote\traits\ModuleTrait;
use yii\base\Model;
use yii\helpers\ArrayHelper;

/**
 * @package common\modules\vote\models
 */
class VoteForm extends Model
{
    use ModuleTrait;

    const ACTION_POSITIVE = 'positive';
    const ACTION_NEGATIVE = 'negative';
    const ACTION_TOGGLE = 'toggle';

    /**
     * @var string entity (e.g. "userLike" or "pageVoting")
     */
    public $entity;

    /**
     * @var integer entity_id
     */
    public $entityId;

    /**
     * @var string +/-?
     */
    public $action;

    /**
     * @return array
     * @throws \yii\base\InvalidConfigException
     */
    public function rules() {
        return [
            [['entity', 'entityId', 'action'], 'required'],
            ['entityId', 'integer'],
            ['action', 'in', 'range' => [self::ACTION_NEGATIVE, self::ACTION_POSITIVE, self::ACTION_TOGGLE]],
            ['entity', 'checkModel'],
        ];
    }

    /**
     * @return int
     */
    public function getValue() {
        return $this->action == self::ACTION_NEGATIVE ? Vote::VOTE_NEGATIVE : Vote::VOTE_POSITIVE;
    }

    /**
     * @return bool
     * @throws \yii\base\InvalidConfigException
     */
    public function checkModel() {
        $module = $this->getModule();
        $settings = $module->getSettingsForEntity($this->entity);
        if ($settings === null) {
            $this->addError('entity', Yii::t('vote', 'error_entity_is_not_supported'));
            return false;
        }
        
        $allowGuests = ArrayHelper::getValue($settings, 'allowGuests', false);
        if (Yii::$app->user->isGuest && ($settings['type'] == Module::TYPE_TOGGLE || !$allowGuests)) {
            $this->addError('entity', Yii::t('vote', 'error_guests_not_allowed'));
            return false;
        }
        
        $targetModel = Yii::createObject($settings['modelName']);
        $entityModel = $targetModel->find()->where(['id' => $this->entityId])->one();
        if ($entityModel == null) {
            $this->addError('entityId', Yii::t('vote', 'error_entity_model_not_found'));
            return false;
        }
        
        $allowSelfVote = ArrayHelper::getValue($settings, 'allowSelfVote', false);
        if (!$allowSelfVote) {
            $entityAuthorAttribute = ArrayHelper::getValue($settings, 'entityAuthorAttribute', 'user_id');
            if (!Yii::$app->user->isGuest && Yii::$app->user->id == $entityModel->{$entityAuthorAttribute}) {
                $this->addError('entity', Yii::t('vote', 'error_self_voting_not_allowed'));
                return false;
            }
        }

        return true;
    }
}
