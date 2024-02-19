<?php
namespace common\modules\cdek\migrations;

use yii\db\Migration;

/**
 * Handles the creation for table `cdek_city`.
 */
class m201022_171402_create_cdek_city extends Migration
{
	/**
	 * @inheritdoc
	 */
	public function up() {

		// create table
		$this->createTable('{{%cdek_city}}', [
			'id' => $this->primaryKey(),
            'country_code' => $this->integer(11),
            'post_code' => $this->integer(11),
            'city_dd' => $this->integer(11),
			'full_name' => $this->string(255),
            'full_name_eng' => $this->string(255),
			'name' => $this->string(255),
			'name_eng' => $this->string(255),
            'country' => $this->string(255),
            'country_en' => $this->string(255),
			'region' => $this->string(255),
            'region_en' => $this->string(255),
            'fias' => $this->string(255),
            'fias_full_name' => $this->string(255),
            'kladr' => $this->string(255),
            'pvz_code' => $this->string(255),
		]);

		// creates index
		$this->createIndex('idx-cdek_city-status', '{{%cdek_city}}', ['country_code']);
	}

	/**
	 * @inheritdoc
	 */
	public function down() {

		// drop table
		$this->dropTable('{{%cdek_city}}');
	}
}
