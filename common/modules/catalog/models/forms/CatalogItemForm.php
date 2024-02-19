<?php
namespace common\modules\catalog\forms;

use api\models\catalog\CatalogItem;

/**
 * Class CatalogItemForm
 * @package common\modules\catalog\forms
 */
class CatalogItemForm extends CatalogItem
{
    function beforeValidate() {
        print_r('test');die;
        return parent::beforeValidate();
    }
}