<?php
namespace common\modules\company\migrations;

use yii\db\Migration;

class m201022_201700_add_fields_to_company_table extends Migration
{
	public function up() {
		$this->addColumn('{{%company}}', 'cdek_postcode', $this->integer(11)->null()->after('ok'));
        $this->addColumn('{{%company}}', 'cdek_country_id', $this->integer(11)->null()->after('cdek_postcode'));
        $this->addColumn('{{%company}}', 'cdek_city_id', $this->integer(11)->null()->after('cdek_country_id'));
        $this->addColumn('{{%company}}', 'cdek_city_name', $this->string(11)->null()->after('cdek_city_id'));
        $this->addColumn('{{%company}}', 'cdek_tariff', $this->integer(11)->null()->after('cdek_city_name'));
        $this->addColumn('{{%company}}', 'cdek_account', $this->string(255)->null()->after('cdek_postcode'));
        $this->addColumn('{{%company}}', 'cdek_secure_password', $this->string(255)->null()->after('cdek_account'));
        $this->addColumn('{{%company}}', 'cdek_test_mode', $this->boolean()->defaultValue(1)->after('cdek_secure_password'));
        $this->addColumn('{{%company}}', 'cdek_enabled', $this->boolean()->defaultValue(0)->after('cdek_test_mode'));
	}

	public function down() {
        $this->dropColumn('{{%company}}', 'cdek_postcode');
        $this->dropColumn('{{%company}}', 'cdek_country_id');
        $this->dropColumn('{{%company}}', 'cdek_city_id');
        $this->dropColumn('{{%company}}', 'cdek_city_name');
        $this->dropColumn('{{%company}}', 'cdek_tariff');
		$this->dropColumn('{{%company}}', 'cdek_account');
		$this->dropColumn('{{%company}}', 'cdek_secure_password');
		$this->dropColumn('{{%company}}', 'cdek_test_mode');
		$this->dropColumn('{{%company}}', 'cdek_enabled');
	}
}
