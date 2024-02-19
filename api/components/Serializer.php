<?php
namespace api\components;

use common\modules\base\components\Debug;
use yii\base\Arrayable;
use yii\base\Model;
use yii\data\DataProviderInterface;
use yii\helpers\ArrayHelper;
use yii\rest\Serializer as BaseSerializer;

/**
 * Class Serializer
 * @package api\components
 */
class Serializer extends BaseSerializer
{
	/**
	 * @var string
	 */
	public $modelEnvelope = 'item';
	
	/**
	 * @var string
	 */
	public $errorEnvelope = 'errors';
	
	/**
	 * @inheritDoc
	 */
	public function serialize($data)
	{
		if ($data instanceof Model && $data->hasErrors()) {
			$data = [$this->errorEnvelope => $this->serializeModelErrors($data)];
		}
		elseif (is_array($data) && isset($data[$this->modelEnvelope])) {
		    $data[$this->modelEnvelope] = $this->serializeModel($data[$this->modelEnvelope]);
        }
        elseif (is_array($data) && isset($data[$this->collectionEnvelope]) && $data[$this->collectionEnvelope] instanceof DataProviderInterface) {
		    $data = ArrayHelper::merge($data, $this->serializeDataProvider($data[$this->collectionEnvelope]));
        }
		elseif ($data instanceof Arrayable) {
			$data = [$this->modelEnvelope => $this->serializeModel($data)];
		}
		elseif ($data instanceof DataProviderInterface) {
            $data = $this->serializeDataProvider($data);
        }
		elseif (is_object($data)) {
		    $data = $this->serializeModel($data);
        }

		if (isset($data['_metaClear']) && $data['_metaClear'] && isset($data['_meta'])) {
		    unset($data['_meta']);
            unset($data['_metaClear']);
        }

        array_walk_recursive($data, function (&$item, $key) {
            $item = null === $item ? '' : $item;
        });
		
		return (isset($data['root'])) ? $data['root'] : $data;
	}
}