<?php

use yii\db\Schema;
use yii\db\Migration;

class m190126_024300_add_fields_to_user_profile_table extends Migration
{
	public function up() {
		//$this->down();
		$this->addColumn('{{%user_profile}}', 'created_by', $this->integer()->defaultValue(0)->after('phone'));
		$this->addColumn('{{%user_profile}}', 'updated_by', $this->integer()->defaultValue(0)->after('created_by'));
		$this->addColumn('{{%user_profile}}', 'created_at', $this->integer()->notNull()->after('updated_by'));
		$this->addColumn('{{%user_profile}}', 'updated_at', $this->integer()->notNull()->after('created_at'));
		
		foreach (\common\modules\user\models\User::find()->all() as $user) {
			$user->profile->created_by = $user->created_by;
			$user->profile->updated_by = $user->updated_by;
			$user->profile->created_at = $user->created_at;
			$user->profile->updated_at = $user->updated_at;
			$user->profile->save();
		}
	}

	public function down() {
		$this->dropColumn('{{%user_profile}}', 'created_by');
		$this->dropColumn('{{%user_profile}}', 'updated_by');
		$this->dropColumn('{{%user_profile}}', 'created_at');
		$this->dropColumn('{{%user_profile}}', 'updated_at');
	}
}
