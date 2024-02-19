<?php
namespace api\models\company;

use common\modules\base\helpers\enum\ModuleType;
use common\modules\company\models\Company as BaseCompany;
use common\modules\seo\models\Seo;
use yii\db\ActiveQuery;

/**
 * Class Company
 * @package api\models\company
 */
class Company extends BaseCompany
{
	/**
	 * @inheritdoc
	 *
	 * The default implementation returns the names of the columns whose values have been populated into this record.
	 */
	public function fields() {
		return [
		    'id',
            'title',
            'descr',
            'slug',
            'image' => function($data) {
			    return $data->getMediaImage();
		    },
            'owner',
            'date_at'
        ];
	}

	/**
	 * @inheritdoc
	 *
	 * The default implementation returns the names of the relations that have been populated into this record.
	 */
	public function extraFields() {
		return [];
	}

    /**
     * @return ActiveQuery
     */
    public function getSeoRelation() {
        return $this->hasOne(Seo::class, ['module_id' => 'id'])->onCondition([
            Seo::tableName().'.module_type' => ModuleType::COMPANY
        ])->where([]);
    }
	
	/**
	 * @return array|null
	 */
	public function getMediaImage() {
		$image = $this->logo;
		if ($image) {
			$imageInfo = $image->getImageInfo(true);
			return [
				'path' => $imageInfo['http'],
				'file' => $imageInfo['file'],
			];
		}
		return null;
	}

    /**
     * @return string
     */
    public function getSlug() {
        return $this->seoRelation ? $this->seoRelation->slugify : '';
    }
}