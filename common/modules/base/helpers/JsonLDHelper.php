<?php
namespace common\modules\base\helpers;

use Yii;
use yii\base\BaseObject;
use yii\helpers\Html;
use yii\helpers\Url;

use ML\JsonLD\JsonLD;

/**
 * Class JsonLDHelper
 */
class JsonLDHelper extends BaseObject
{
    /**
     * Adds BreadcrumbList schema.org markup based on the application view `breadcrumbs` parameter
     */
    public static function addBreadcrumbList() {
        $view = Yii::$app->getView();

        $breadcrumbList = [];
        if (isset($view->params['breadcrumbs'])) {
            $position = 1;
            foreach ($view->params['breadcrumbs'] as $breadcrumb) {
                if (is_array($breadcrumb)) {
                    $breadcrumbList[] = (object)[
                        "@type" => "http://schema.org/ListItem",
                        "http://schema.org/position" => $position,
                        "http://schema.org/item" => (object)[
                            "@id" => Url::to($breadcrumb['url'], true),
                            "http://schema.org/name" => $breadcrumb['label'],
                        ]
                    ];
                } else {
                    $breadcrumbList[] = (object)[
                        "@type" => "http://schema.org/ListItem",
                        "http://schema.org/position" => $position,
                        "http://schema.org/item" => (object)[
                            "http://schema.org/name" => $breadcrumb,
                        ]
                    ];
                }
                $position++;
            }
        }

        $doc = (object)[
            "@type" => "http://schema.org/BreadcrumbList",
            "http://schema.org/itemListElement" => $breadcrumbList
        ];

        JsonLDHelper::add($doc);
    }

    /**
     * Compacts JSON-LD document, encodes and adds to the application view `jsonld` parameter,
     * so it can later be registered using JsonLDHelper::registerScripts().
     *
     * @param array|object $doc The JSON-LD document
     * @param array|null|object|string $context optional context. If not specified, schema.org vocabulary will be used.
     */
    public static function add($doc, $context = null) {
        if (is_null($context)) {
            $context = (object)["@context" => "http://schema.org"];
        }

        $compacted = JsonLD::compact((object)$doc, $context);

        $view = Yii::$app->getView();
        $view->params['jsonld'][] = json_encode($compacted, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }

    /**
     * Registers JSON-LD scripts stored in the application view `jsonld` parameter.
     * This should be invoked in the <head> section of your layout.
     */
    public static function registerScripts() {
        $view = Yii::$app->getView();

        if (isset($view->params['jsonld'])) {
            foreach ($view->params['jsonld'] as $jsonld) {
                echo Html::script($jsonld, ['type' => 'application/ld+json']);
            }
        }
    }
}