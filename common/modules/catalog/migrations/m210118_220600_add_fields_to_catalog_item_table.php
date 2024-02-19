<?php
namespace common\modules\catalog\migrations;

use yii\db\Migration;

class m210118_220600_add_fields_to_catalog_item_table extends Migration
{
	public function up() {
        $this->addColumn('{{%catalog_item}}', 'description', $this->text()->null()->after('title'));
		$this->addColumn('{{%catalog_item}}', 'sequence', $this->integer()->defaultValue(0)->after('in_stock'));
        $this->addColumn('{{%catalog_item}}', 'is_sale', $this->boolean()->defaultValue(false)->after('sequence'));
        $this->addColumn('{{%catalog_item}}', 'is_sprut', $this->boolean()->defaultValue(false)->after('is_sale'));
        $this->addColumn('{{%catalog_item}}', 'sprut_type', $this->integer()->null()->after('is_sprut'));
        $this->addColumn('{{%catalog_item}}', 'sprut_content_json', $this->text()->null()->after('sprut_type'));
	}

	public function down() {
        $this->dropColumn('{{%catalog_item}}', 'description');
        $this->dropColumn('{{%catalog_item}}', 'sequence');
        $this->dropColumn('{{%catalog_item}}', 'is_sale');
        $this->dropColumn('{{%catalog_item}}', 'is_sprut');
        $this->dropColumn('{{%catalog_item}}', 'sprut_type');
        $this->dropColumn('{{%catalog_item}}', 'sprut_content_json');
	}
}