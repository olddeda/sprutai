<?php
namespace common\modules\catalog\migrations;

use yii\db\Migration;

use common\modules\base\helpers\enum\ModuleType;
use common\modules\catalog\models\CatalogItem;
use common\modules\seo\models\Seo;

class m200103_183700_apply_seo_to_catalog_item_table extends Migration
{
	public function up() {
		$models = CatalogItem::find()->all();

		Seo::deleteAll('module_type = :module_type', [
		    ':module_type' => ModuleType::CATALOG_ITEM
        ]);

		foreach ($models as $model) {
		    $model->seo->save();
		    echo $model->getSlugify_title()." => ".$model->seo->slugify.PHP_EOL;
        }
	}

	public function down() {
	}
}