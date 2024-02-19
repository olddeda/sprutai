<?php
use common\modules\user\migrations\Migration;

class m180805_174125_create_address_table extends Migration
{
    public function up() {
        $this->createTable('{{%user_address}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer(),
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
        ], $this->tableOptions);

        $this->addForeignKey('fk_user_address', '{{%user_address}}', 'user_id', '{{%user}}', 'id', 'CASCADE', 'RESTRICT');
    }

    public function down() {
        $this->dropTable('{{%user_address}}');
    }
}
