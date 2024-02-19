<?php
namespace api\models\content;

use Yii;


/**
 * Class ContentActivity
 * @package api\models\content
 */

class ContentActivity extends Content
{
    /**
     * @param array $row
     *
     * @return Content
     */
    public static function instantiate($row) {
        return new self();
    }

    /**
	 * @inheritdoc
	 *
	 * The default implementation returns the names of the columns whose values have been populated into this record.
	 */
	public function fields() {
		return [
			'id',
			'slug',
			'title' => function($data) {
		        return html_entity_decode($data->title);
            },
			'type' => function($data) {
				return $data->getType();
			},
			'date_at',
		];
	}
}