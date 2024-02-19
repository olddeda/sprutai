<?php
namespace api\traits;

/**
 * Trait ImageTrait
 */
trait ImageTrait {
    /**
     * @return array|null
     */
    public function mediaImageFor($field) {
        $image = $this->$field;
        if ($field == 'mediaMobile') {
            print_r($image->getImageInfo(true));die;
        }
        if ($image && is_object($image))
        {
            $imageInfo = $image->getImageInfo(true);
            return [
                'http' => $imageInfo['http'],
                'path' => $imageInfo['path'],
                'file' => $imageInfo['file'],
                'original' => $imageInfo['original'],
            ];
        }
        return null;
    }
}