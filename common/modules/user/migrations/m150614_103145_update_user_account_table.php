<?php

use yii\db\Query;
use yii\db\Schema;
use yii\db\Migration;


use common\modules\user\models\User;

class m150614_103145_update_user_account_table extends Migration
{
    public function up() {
	    $db = User::getDb();
        $this->addColumn('{{%user_account}}', 'code', Schema::TYPE_STRING . '(32)');
        $this->addColumn('{{%user_account}}', 'email', Schema::TYPE_STRING);
        $this->addColumn('{{%user_account}}', 'username', Schema::TYPE_STRING);
		$this->addColumn('{{%user_account}}', 'created_at', Schema::TYPE_INTEGER);
		$this->addColumn('{{%user_account}}', 'updated_at', Schema::TYPE_INTEGER);
        $this->createIndex('account_unique_code', '{{%user_account}}', 'code', true);

        $accounts = (new Query())->from('{{%user_account}}')->select('id')->all($db);

        $transaction = Yii::$app->db->beginTransaction();
        try {
            foreach ($accounts as $account) {
                $db->createCommand()->update('{{%user_account}}', [
                    'created_at' => time(),
					'updated_at' => time(),
                ], 'id = '.$account['id'])->execute();
            }
            $transaction->commit();
        } catch (Exception $e) {
            $transaction->rollBack();
            throw $e;
        }
    }

    public function down() {
        $this->dropIndex('account_unique_code', '{{%user_account}}');
        $this->dropColumn('{{%user_account}}', 'email');
        $this->dropColumn('{{%user_account}}', 'username');
        $this->dropColumn('{{%user_account}}', 'code');
        $this->dropColumn('{{%user_account}}', 'created_at');
		$this->dropColumn('{{%user_account}}', 'updated_at');
    }
}
