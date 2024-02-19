<?php

namespace common\modules\user\widgets;

use Yii;
use yii\authclient\ClientInterface;
use yii\authclient\widgets\AuthChoice;
use yii\authclient\widgets\AuthChoiceAsset;
use yii\helpers\Html;
use yii\helpers\Url;

class Connect extends AuthChoice
{
    /**
     * @var array|null An array of user's accounts
     */
    public $accounts;

    /**
     * @inheritdoc
     */
    public $options = [];

    /**
     * @inheritdoc
     */
    public function init() {
        AuthChoiceAsset::register(Yii::$app->view);
        if ($this->popupMode) {
            Yii::$app->view->registerJs("\$('#" . $this->getId() . "').authchoice();");
        }
        $this->options['id'] = $this->getId();
        echo Html::beginTag('div', $this->options);
    }
	
	/**
	 * Outputs client auth link.
	 * @param ClientInterface $client external auth client instance.
	 * @param string $text link text, if not set - default value will be generated.
	 * @param array $htmlOptions link HTML options.
	 * @return string generated HTML.
	 * @throws InvalidConfigException on wrong configuration.
	 */
	public function clientLink($client, $text = null, array $htmlOptions = [])
	{
		$viewOptions = $client->getViewOptions();
		
		if (empty($viewOptions['widget'])) {
			if ($text === null) {
				$text = Html::tag('span', '', ['class' => 'fa fa-'.$client->getIcon()]);
			}
			if (!isset($htmlOptions['class'])) {
				$htmlOptions['class'] = $client->getName();
			}
			if (!isset($htmlOptions['title'])) {
				$htmlOptions['title'] = $client->getTitle();
			}
			Html::addCssClass($htmlOptions, ['widget' => 'auth-link']);
			
			if ($this->popupMode) {
				if (isset($viewOptions['popupWidth'])) {
					$htmlOptions['data-popup-width'] = $viewOptions['popupWidth'];
				}
				if (isset($viewOptions['popupHeight'])) {
					$htmlOptions['data-popup-height'] = $viewOptions['popupHeight'];
				}
			}
			return Html::a($text, $this->createClientUrl($client), $htmlOptions);
		}
		
		$widgetConfig = $viewOptions['widget'];
		if (!isset($widgetConfig['class'])) {
			throw new InvalidConfigException('Widget config "class" parameter is missing');
		}
		/* @var $widgetClass Widget */
		$widgetClass = $widgetConfig['class'];
		if (!(is_subclass_of($widgetClass, AuthChoiceItem::className()))) {
			throw new InvalidConfigException('Item widget class must be subclass of "' . AuthChoiceItem::className() . '"');
		}
		unset($widgetConfig['class']);
		$widgetConfig['client'] = $client;
		$widgetConfig['authChoice'] = $this;
		return $widgetClass::widget($widgetConfig);
	}
	
	/**
	 * Renders the main content, which includes all external services links.
	 * @return string generated HTML.
	 */
	protected function renderMainContent() {
		$items = [];
		foreach ($this->getClients() as $externalService) {
			if ($externalService->getId() !== 'telegram' && isset($externalService->validateAuthState) && $externalService->validateAuthState) {
                $items[] = Html::tag('li', $this->clientLink($externalService));
            }
		}
		return Html::tag('ul', implode('', $items), ['class' => 'auth-clients']);
	}

    /**
     * @inheritdoc
     */
    public function createClientUrl($provider) {
        if ($this->getAccount($provider)) {
            return Url::to(['/user/settings/disconnect', 'id' => $this->accounts[$provider->getId()]->id]);
        }
		else {
            return parent::createClientUrl($provider);
        }
    }

    /**
     * Checks if provider already connected to user.
     *
     * @param ClientInterface $provider
     *
     * @return bool
     */
    public function isConnected(ClientInterface $provider) {
    	$account = $this->getAccount($provider);
    	if (!is_null($account)) {
			return ($account->client_id) ? true : false;
		}
		return false;
    }
    
    public function getAccount(ClientInterface $provider) {
		if ($this->accounts != null && isset($this->accounts[$provider->getId()]))
			return $this->accounts[$provider->getId()];
		return null;
	}
}
