<?php
namespace common\modules\tag\migrations;

use common\modules\tag\helpers\enum\Type;
use yii\db\Migration;

use common\modules\base\helpers\enum\Status;
use common\modules\tag\models\Tag;

class m191130_154500_change_type_to_bitmask_tag_table extends Migration
{
    const NONE						= 0;
    const SYSTEM					= 1;
    const QA						= 50;
    const SPECIAL					= 99;
    const ALL						= 100;

    public static $list = [
        self::NONE 		=> 'none',
        self::SYSTEM	=> 'system',
        self::SPECIAL	=> 'special',
        self::ALL		=> 'all',
    ];

    public function up() {

        $tags = Tag::findAll(['status' => Status::ENABLED]);
        if ($tags) {
            foreach ($tags as $tag) {
                if (isset(self::$list[$tag->type])) {
                    $tag->type = array_search(self::$list[$tag->type], Type::$list);
                    //$tag->save(false);
                }
            }
        }
	}

	public function down() {
		$this->dropColumn('{{%tag_nested}}', 'module_type');
		$this->dropColumn('{{%tag_nested}}', 'module_id');
	}
}