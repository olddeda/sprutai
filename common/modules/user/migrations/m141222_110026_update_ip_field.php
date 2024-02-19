<?php

use yii\db\Migration;
use yii\db\Query;
use yii\db\Schema;

use common\modules\user\models\User;

class m141222_110026_update_ip_field extends Migration
{
    public function up() {
    	$db = User::getDb();
        $users = (new Query())->from('{{%user}}')->select('id, registration_ip ip')->all($db);

        $transaction = Yii::$app->db->beginTransaction();
        try {
            $this->alterColumn('{{%user}}', 'registration_ip', Schema::TYPE_STRING . '(45)');
            foreach ($users as $user) {
                if ($user['ip'] == null) {
                    continue;
                }
                $db->createCommand()->update('{{%user}}', [
                    'registration_ip' => long2ip($user['ip']),
                ], 'id = '.$user['id'])->execute();
            }
            $transaction->commit();
        } catch (Exception $e) {
            $transaction->rollBack();
            throw $e;
        }
    }

    public function down() {
	    $db = User::getDb();
        $users = (new Query())->from('{{%user}}')->select('id, registration_ip ip')->all($db);

        $transaction = Yii::$app->db->beginTransaction();
        try {
            foreach ($users as $user) {
                if ($user['ip'] == null)
                    continue;
                $db->createCommand()->update('{{%user}}', [
                    'registration_ip' => ip2long($user['ip'])
                ], 'id = '.$user['id'])->execute();
            }
            $this->alterColumn('{{%user}}', 'registration_ip', Schema::TYPE_BIGINT);
            $transaction->commit();
        } catch (Exception $e) {
            $transaction->rollBack();
            throw $e;
        }
    }
}
