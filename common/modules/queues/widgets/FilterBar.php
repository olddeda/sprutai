<?php
namespace common\modules\queues\widgets;

use yii\base\Widget;
use yii\bootstrap\BootstrapPluginAsset;
use yii\bootstrap\Html;

/**
 * Class FilterBar
 * @package common\modules\queues\widgets
 */
class FilterBar extends Widget
{
    /**
     * @inheritdoc
     */
    public function init() {
        parent::init();
        ob_start();
        ob_implicit_flush(false);
    }

    /**
     * @inheritdoc
     */
    public function run() {
        BootstrapPluginAsset::register($this->view);
        $this->view->registerJs(
            <<<JS
            $('#queue-filter-bar').affix({offset: {top: 60}});
JS
        );
        $this->view->registerCss(
            <<<CSS
            #queue-filter-bar {
                margin-bottom: 20px;
            }
            #queue-filter-bar.affix {
                position: inherit;
            }
            
            @media (min-width: 1200px) {
                #queue-filter-bar.affix {
                    position: fixed;
                    top: 60px;
                    width: 262px;
                }
            }
CSS
        );
        return Html::tag('div', ob_get_clean(), [
            'id' => 'queue-filter-bar',
        ]);
    }
}
