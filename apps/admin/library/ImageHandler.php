<?php
namespace apps\admin\library;


use Phalcon\Annotations\Exception;
use Phalcon\Image;

class ImageHandler
{

    /**
     * @var ImageHandler
     */
    private static $istance = null;
    public $imageVersions = [];

    private function __construct()
    {
        $this->imageVersions = self::getImageVersions();
    }

    protected static function getImageVersions()
    {
        $filesSizes = \FilesSizes::find();

        $imageVersions = ['thumbnail' => [
            'max_width'  => 80,
            'max_height' => 80
        ]];

        foreach ($filesSizes as $fsize) {
            $imageVersions[$fsize->key] = [
                'crop'       => $fsize->crop == '1' ?: false,
                'max_width'  => $fsize->max_width,
                'max_height' => $fsize->max_height
            ];
        }
        return $imageVersions;
    }

    public static function getIstance()
    {
        if (is_null(self::$istance)) {
            self::$istance = new self();
        }
        return self::$istance;
    }

    /**
     * @param $file \Files | int
     * @return bool
     */
    public function regenerateThumbnails($file, $size = null)
    {
        if ($file instanceof \Files) {
            $fileResource = $file;
        } elseif (is_numeric($file)) {
            $fileResource = \Files::findFirstById($file);
        } else {
            return false;
        }
        if (!$fileResource) return false;


        $file_path = !$fileResource->private ? FILES_DIR . $fileResource->filename : FILES_DIR . 'reserved' . DIRECTORY_SEPARATOR . $fileResource->filename;

        if (!is_null($size)) {
            if (!file_exists($file_path)) {
                return false;
            }
            try {
                if (extension_loaded('imagick')) {
                    $image = new \Phalcon\Image\Adapter\Imagick($file_path);
                } else {
                    $image = new \Phalcon\Image\Adapter\Gd($file_path);
                }
            } catch (\ImagickException $e) {
                \PhalconDebug::debug($e->getMessage());
                return false;
            } catch (\Exception $e) {
                \PhalconDebug::debug($e->getMessage());
                return false;
            }
            $image->resize(
                $this->imageVersions[$size]['max_width'],
                null,
                Image::WIDTH
            );
            if ($image->getHeight() > $this->imageVersions[$size]['max_height']) {
                $offsetY = (($image->getHeight() - $this->imageVersions[$size]['max_height']) / 2);
                $image->crop($this->imageVersions[$size]['max_width'], $this->imageVersions[$size]['max_height'], null, $offsetY);
            }

            $size_dir = !$file->private ? FILES_DIR . $size : FILES_DIR . 'reserved' . DIRECTORY_SEPARATOR . $size;
            if (!file_exists($size_dir)) {
                mkdir($size_dir, 0755);
            }
            $file_save_path = !$file->private ? FILES_DIR . $size . DIRECTORY_SEPARATOR . $fileResource->filename : FILES_DIR . 'reserved' . DIRECTORY_SEPARATOR . $size . DIRECTORY_SEPARATOR . $fileResource->filename;
            $image->save($file_save_path, 90);
            unset($image);
            return true;
        } else {
            foreach ($this->imageVersions as $key => $set) {
                if (!file_exists($file_path)) {
                    return false;
                }
                if (extension_loaded('imagick')) {
                    $image = new \Phalcon\Image\Adapter\Imagick($file_path);
                } else {
                    $image = new \Phalcon\Image\Adapter\Gd($file_path);
                }
                $image->resize(
                    $set['max_width'],
                    null,
                    Image::WIDTH
                );
                if ($image->getHeight() > $set['max_height']) {
                    $offsetY = (($image->getHeight() - $set['max_height']) / 2);
                    $image->crop($set['max_width'], $set['max_height'], null, $offsetY);
                }

                if (!file_exists(FILES_DIR . $key)) {
                    mkdir(FILES_DIR . $key, 0755);
                }

                $image->save(FILES_DIR . $key . DIRECTORY_SEPARATOR . $fileResource->filename, 90);
                unset($image);
            }
            return true;
        }
    }
}