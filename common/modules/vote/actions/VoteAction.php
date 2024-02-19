<?php
namespace common\modules\vote\actions;

use Yii;
use yii\base\Action;
use yii\web\MethodNotAllowedHttpException;
use yii\web\Response;

use common\modules\vote\Module;
use common\modules\vote\events\VoteActionEvent;
use common\modules\vote\models\Vote;
use common\modules\vote\models\VoteAggregate;
use common\modules\vote\models\VoteForm;
use common\modules\vote\traits\ModuleTrait;

/**
 * Class VoteAction
 * @package common\modules\vote\actions
 */
class VoteAction extends Action
{
    use ModuleTrait;

    const EVENT_BEFORE_VOTE = 'beforeVote';
    const EVENT_AFTER_VOTE = 'afterVote';

    /**
     * @return array
     * @throws MethodNotAllowedHttpException
     */
    public function run() {
        if (!Yii::$app->request->getIsAjax() || !Yii::$app->request->getIsPost()) {
            throw new MethodNotAllowedHttpException(Yii::t('vote', 'Forbidden method'), 405);
        }

        Yii::$app->response->format = Response::FORMAT_JSON;
        
        $module = $this->getModule();
        $form = new VoteForm();
        $form->load(Yii::$app->request->post());
        $this->trigger(self::EVENT_BEFORE_VOTE, $event = $this->createEvent($form, $response = []));

        if ($form->validate()) {
            $settings = $module->getSettingsForEntity($form->entity);
            if ($settings['type'] == Module::TYPE_VOTING) {
                $response = $this->processVote($form);
            }
            else {
                $response = $this->processToggle($form);
            }
            
            $response = array_merge($event->responseData, $response);
            $response['aggregate'] = VoteAggregate::findOne([
                'entity' => $module->encodeEntity($form->entity),
                'entity_id' => $form->entityId,
            ]);
        }
        else {
            $response = [
            	'success' => false,
				'errors' => $form->errors
			];
        }

        $this->trigger(self::EVENT_AFTER_VOTE, $event = $this->createEvent($form, $response));

        return $event->responseData;
    }

    /**
     * Processes a vote (+/-) request.
     *
     * @param VoteForm $form
     * @return array
     * @throws \Exception
     * @throws \yii\base\InvalidConfigException
     */
    protected function processVote(VoteForm $form) {
    	
        /* @var $vote Vote */
        $module = $this->getModule();
        $response = ['success' => false];
        $searchParams = ['entity' => $module->encodeEntity($form->entity), 'entity_id' => $form->entityId];

        if (Yii::$app->user->isGuest) {
            $vote = Vote::find()
                ->where($searchParams)
                ->andWhere(['user_ip' => Yii::$app->request->userIP])
                ->andWhere('UNIX_TIMESTAMP() - created_at < :limit', [':limit' => $module->guestTimeLimit])
                ->one();
        }
        else {
            $vote = Vote::findOne(array_merge($searchParams, ['user_id' => Yii::$app->user->id]));
        }

        if ($vote == null) {
            $response = $this->createVote($module->encodeEntity($form->entity), $form->entityId, $form->getValue());
        }
        else {
            if ($vote->value !== $form->getValue()) {
                $vote->value = $form->getValue();
                if ($vote->save()) {
                    $response = ['success' => true, 'changed' => true];
                }
            }
        }

        return $response;
    }

    /**
     * Processes a vote toggle request (like/favorite etc).
     *
     * @param VoteForm $form
     * @return array
     * @throws \Exception
     * @throws \yii\base\InvalidConfigException
     */
    protected function processToggle(VoteForm $form) {
    	
        /* @var $vote Vote */
        $module = $this->getModule();
        $vote = Vote::findOne([
            'entity' => $module->encodeEntity($form->entity),
            'entity_id' => $form->entityId,
            'user_id' => Yii::$app->user->id
        ]);

        if ($vote == null) {
            $response = $this->createVote($module->encodeEntity($form->entity), $form->entityId, $form->getValue());
            $response['toggleValue'] = 1;
        }
        else {
            $vote->delete(false);
            $response = [
            	'success' => true,
				'toggleValue' => 0
			];
        }

        return $response;
    }

    /**
     * Creates new vote entry and returns response data.
     *
     * @param string $entity
     * @param integer $entityId
     * @param integer $value
     * @return array
     */
    protected function createVote($entity, $entityId, $value) {
        $vote = new Vote();
        $vote->entity = $entity;
        $vote->entity_id = $entityId;
        $vote->value = $value;

        if ($vote->save()) {
            return [
            	'success' => true
			];
        }
        else {
            return [
            	'success' => false,
				'errors' => $vote->errors
			];
        }
    }

    /**
     * @param VoteForm $voteForm
     * @param array $responseData
     * @return object
     * @throws \yii\base\InvalidConfigException
     */
    protected function createEvent(VoteForm $voteForm, array $responseData) {
        return Yii::createObject([
            'class' => VoteActionEvent::className(),
            'voteForm' => $voteForm,
            'responseData' => $responseData
        ]);
    }
}
