<?php
namespace common\modules\plugin\models;

use common\modules\plugin\helpers\enum\RepositoryProvider;
use yii\base\Model;
use yii\helpers\Json;


/**
 * @property int $provider
 * @property int $created_at
 * @property int $published_at
 * @property string $token
 * @property string $owner
 * @property string $name
 * @property string $tag
 * @property string $reference
 */

class VersionRepository extends Model
{
	/** @var integer */
	public $provider;
	
	/** @var integer */
	public $created_at;
	
	/** @var integer */
	public $published_at;
	
	/** @var string */
	public $token;
	
	/** @var string */
	public $owner;
	
	/** @var string */
	public $name;
	
	/** @var string */
	public $tag;
	
	/** @var string */
	public $reference;
	
	/**
	 * {@inheritdoc}
	 */
	public function rules() {
		return [
			[['provider', 'created_at', 'published_at'], 'integer'],
			[['name', 'token', 'owner', 'tag', 'reference'], 'string', 'max' => 255],
		];
	}
	
	/**
	 * Create instance from array
	 * @param array $data
	 *
	 * @return VersionRepository
	 */
	static public function fromArray($data) {
		if (is_null($data))
			$data = [];
		else if (is_string($data))
			$data = Json::decode($data);
		return new self($data);
	}
	
	/**
	 * Return json string
	 * @return string
	 */
	public function toString() {
		return Json::encode($this->toArray());
	}
	
	/**
	 * Get link for create repository
	 * @return string
	 */
	public function getCreateRepositoryUrl() {
		switch ($this->provider) {
			case RepositoryProvider::GITHUB: {
				return 'https://github.com/new';
			}
			case RepositoryProvider::BITBUCKET: {
			}
		}
	}
	
	/**
	 * Get link for create release
	 * @return string
	 */
	public function getCreateReleaseUrl() {
		switch ($this->provider) {
			case RepositoryProvider::GITHUB: {
				return 'https://github.com/'.$this->owner.'/'.$this->name.'/releases/new';
			}
			case RepositoryProvider::BITBUCKET: {
			}
		}
	}
}