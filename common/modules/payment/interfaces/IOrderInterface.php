<?php
namespace common\modules\payment\interfaces;

/**
 * Interface IOrderInterface
 * @package common\modules\payment\interfaces
 */
interface IOrderInterface
{

    /**
     * @param string|int $id
     * @return string|null
     */
    public static function findOrderStateById($id);

}