<?php
namespace common\modules\comments\models;

use Yii;
use yii\helpers\Url;

/**
 * This is the model class for content owner.
 *
 * @property string $title
 * @property string $type
 * @property string $url
 * @property bool $isCompany
 */
class CommentOwner
{
	/**
	 * @var string
	 */
	public $title;
	
	/**
	 * @var string
	 */
	public $type;
	
	/**
	 * @var string
	 */
	public $url;
	
	/**
	 * @var bool
	 */
	public $isCompany;
	
	/**
	 * CommentOwner constructor.
	 *
	 * @param Comment $model
	 */
	public function __construct(Comment $model) {
		$this->isCompany = $model->getIsCompany();
		$this->title = ($this->isCompany) ? $model->company->title : $model->author->getAuthorName();
		$this->url = ($this->isCompany) ? Url::to(['/'.$model->company->getUriModuleName().'/view', 'id' => $model->company_id]) : Url::to(['/user/profile/view', 'id' => $model->created_by]);
		$this->type = ($this->isCompany) ? Yii::t('content', 'author_type_company') : Yii::t('content', 'author_type_user');
	}
}