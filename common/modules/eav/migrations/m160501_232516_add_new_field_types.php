<?php

namespace common\modules\eav\migrations;

use yii\db\Migration;

use common\modules\eav\handlers\ValueHandler;

class m160501_232516_add_new_field_types extends Migration
{
	public function up() {
		$this->insert('{{%eav_attribute_type}}', [
			'name' => 'numiric',
			'storeType' => ValueHandler::STORE_TYPE_RAW,
			'handlerClass' => '\common\modules\eav\widgets\NumericInput',
		]);
		
	}
	
	public function down() {
		echo "m160501_232516_add_new_field_types cannot be reverted.\n";
		
		return false;
	}
}
