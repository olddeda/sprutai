<?php
namespace common\modules\vote\migrations;

use Yii;

/**
 * @package common\modules\vote\migrations
 * @property \common\modules\vote\Module $module
 */
class Migration extends \yii\db\Migration
{
    /**
     * @var string
     */
    protected $tableOptions;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        $this->tableOptions = null;
        if (Yii::$app->db->driverName == 'mysql') {
            $this->tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }
    }
}
