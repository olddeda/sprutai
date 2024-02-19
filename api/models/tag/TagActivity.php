<?php
namespace api\models\tag;

/**
 * Class TagActivity
 * @package api\models\tag
 */
class TagActivity extends Tag
{
    /**
     * @inheritdoc
     */
    public function fields(): array
    {
        return [
            'id',
            'title',
        ];
    }
}
