<?php
namespace common\modules\base\extensions\sitemap;

/**
 * Interface SitemapInterface
 * @package common\modules\base\extensions\sitemap
 */
interface SitemapInterface
{
    /**
     * @return string
     */
    public function getSitemapUrl();


    /**
     * @return \yii\db\ActiveQuery
     */
    public static function sitemap();
}