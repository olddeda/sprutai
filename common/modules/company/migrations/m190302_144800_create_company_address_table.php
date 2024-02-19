<?php
namespace common\modules\company\migrations;

use yii\db\Migration;

class m190302_144800_create_company_address_table extends Migration
{
    public function up() {
        $this->createTable('{{%company_address}}', [
            'id' => $this->primaryKey(),
            'company_id' => $this->integer(),
			'is_primary' => $this->boolean()->defaultValue(false),
	        'address' => $this->text(),
	        'postal_code' => $this->string(10),
            'country' => $this->string(100),
	        'region' => $this->string(100),
	        'city' => $this->string(100),
	        'street' => $this->string(100),
	        'house' => $this->string(20),
	        'block' => $this->string(20),
	        'flat' => $this->string(20),
	        'metro' => $this->string(100),
	        'created_at' => $this->integer()->notNull(),
	        'updated_at' => $this->integer()->notNull(),
        ]);

        $this->addForeignKey('fk_company_address_company', '{{%company_address}}', 'company_id', '{{%company}}', 'id', 'CASCADE', 'RESTRICT');
    }

    public function down() {
        $this->dropTable('{{%company_address}}');
    }
}
