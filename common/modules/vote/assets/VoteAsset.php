<?php
namespace common\modules\vote\assets;

use yii\web\AssetBundle;

/**
 * @package common\modules\vote\assets
 */
class VoteAsset extends AssetBundle
{
    public $sourcePath = '@common/modules/vote/assets/static';
    
    public $css = [
        'vote.css',
    ];
    
    public $depends = [
        'yii\web\YiiAsset',
    ];
}
