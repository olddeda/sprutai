<?php
namespace common\modules\queues\widgets;

/**
 * Class LinkPager
 * @package common\modules\queues\widgets
 */
class LinkPager extends \yii\widgets\LinkPager
{
    /**
     * @var string
     */
    public $layout = '<div class="pull-right">{sizer}</div> {pager} <div class="clearfix"></div>';
    
    /**
     * @var array of sizer options
     */
    public $sizer = [];
    
    /**
     * @inheritdoc
     */
    public $hideOnSinglePage = false;

    /**
     * @inheritdoc
     */
    public function run() {
        if ($this->registerLinkTags) {
            $this->registerLinkTags();
        }

        return preg_replace_callback('/{\w+}/', function ($matches) {
            return $this->renderSection($matches[0]);
        }, $this->layout);
    }

    public function renderSection($name) {
        switch ($name) {
            case '{pager}':
                return $this->renderPageButtons();
            case '{sizer}':
                return LinkSizer::widget(['pagination' => $this->pagination] + $this->sizer);
            default:
                return false;
        }
    }
}
