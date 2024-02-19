<?php
namespace common\modules\catalog\migrations;

use yii\db\Migration;

class m201023_192300_add_fields_to_catalog_item_order_table extends Migration
{
	public function up() {
		$this->addColumn('{{%catalog_item_order}}', 'postal_code', $this->integer(11)->null()->after('phone'));
        $this->addColumn('{{%catalog_item_order}}', 'country', $this->string(255)->null()->after('postal_code'));
        $this->addColumn('{{%catalog_item_order}}', 'country_code', $this->string(255)->null()->after('country'));
        $this->addColumn('{{%catalog_item_order}}', 'city', $this->string(255)->null()->after('country_code'));
        $this->addColumn('{{%catalog_item_order}}', 'street', $this->string(255)->null()->after('city'));
        $this->addColumn('{{%catalog_item_order}}', 'house', $this->string(255)->null()->after('street'));
        $this->addColumn('{{%catalog_item_order}}', 'block', $this->string(255)->null()->after('house'));
        $this->addColumn('{{%catalog_item_order}}', 'flat', $this->string(255)->null()->after('block'));;
        $this->addColumn('{{%catalog_item_order}}', 'delivery_price', $this->decimal(10,2)->null()->after('delivery_code'));
        $this->addColumn('{{%catalog_item_order}}', 'delivery_currency', $this->string(2)->null()->after('delivery_price'));
        $this->addColumn('{{%catalog_item_order}}', 'delivery_days_min', $this->integer(2)->null()->after('delivery_currency'));
        $this->addColumn('{{%catalog_item_order}}', 'delivery_days_max', $this->integer(2)->null()->after('delivery_days_min'));
	}

	public function down() {
		$this->dropColumn('{{%catalog_item_order}}', 'postal_code');
        $this->dropColumn('{{%catalog_item_order}}', 'country');
        $this->dropColumn('{{%catalog_item_order}}', 'country_code');
        $this->dropColumn('{{%catalog_item_order}}', 'city');
        $this->dropColumn('{{%catalog_item_order}}', 'street');
        $this->dropColumn('{{%catalog_item_order}}', 'house');
        $this->dropColumn('{{%catalog_item_order}}', 'block');
        $this->dropColumn('{{%catalog_item_order}}', 'flat');
        $this->dropColumn('{{%catalog_item_order}}', 'delivery_price');
        $this->dropColumn('{{%catalog_item_order}}', 'delivery_currency');
        $this->dropColumn('{{%catalog_item_order}}', 'delivery_days_min');
        $this->dropColumn('{{%catalog_item_order}}', 'delivery_days_max');
	}
}