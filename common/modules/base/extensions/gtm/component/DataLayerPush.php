<?php
namespace common\modules\base\extensions\gtm\component;

use yii\base\component;
use Yii;

use common\modules\base\extensions\gtm\widget\GTMWidget;

class DataLayerPush extends Component
{
	public function add(array $event): void {
		$session = Yii::$app->getSession();
		$values = $session->get(GTM::SESSION_KEY) ?? [];
		$values[] = $event;
		$session->set(GTM::SESSION_KEY, $values);
	}
}
