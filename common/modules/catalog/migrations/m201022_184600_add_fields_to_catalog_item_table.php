<?php
namespace common\modules\catalog\migrations;

use yii\db\Migration;

class m201022_184600_add_fields_to_catalog_item_table extends Migration
{
	public function up() {
		$this->addColumn('{{%catalog_item}}', 'weight', $this->decimal(10,2)->defaultValue(0)->after('price'));
        $this->addColumn('{{%catalog_item}}', 'length', $this->decimal(10,2)->defaultValue(0)->after('weight'));
        $this->addColumn('{{%catalog_item}}', 'width', $this->decimal(10,2)->defaultValue(0)->after('length'));
        $this->addColumn('{{%catalog_item}}', 'height', $this->decimal(10,2)->defaultValue(0)->after('width'));
	}

	public function down() {
		$this->dropColumn('{{%catalog_item}}', 'weight');
        $this->dropColumn('{{%catalog_item}}', 'length');
        $this->dropColumn('{{%catalog_item}}', 'width');
        $this->dropColumn('{{%catalog_item}}', 'height');
	}
}