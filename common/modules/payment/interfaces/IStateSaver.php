<?php
namespace common\modules\payment\interfaces;

/**
 * Interface IStateSaver
 * @package common\modules\payment\interfaces
 */
interface IStateSaver
{

    /**
     * @param string|int $id
     * @param array $data
     */
    public function set($id, $data);

    /**
     * @param string|int $id
     * @return mixed|null
     */
    public function get($id);

}