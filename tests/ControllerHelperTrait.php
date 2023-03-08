<?php

namespace App\Tests;

trait ControllerHelperTrait
{
    protected function getImages(array $sizes, array $fileTypes): array
    {
        $images = [];
        $tempPath = sys_get_temp_dir();
        $bigfile = imagecreatetruecolor(100, 100);
        foreach ($fileTypes as $type) {
            foreach ($sizes as $size) {
                $w = $size['width'];
                $h = $size['height'];
                $new = imagescale($bigfile, $w, $h);

                $imgFileName = $type . '_' . $w . 'x' . $h . '_px.' . $type;
                $img = $tempPath . '/' . $imgFileName;

                match ($type) {
                    'bmp' => imagebmp($new, $img),
                    'gif' => imagegif($new, $img),
                    'jpg' => imagejpeg($new, $img),
                    'png' => imagepng($new, $img),
                };

                list($newW, $newH) = getimagesize($img);

                $imageOptions = [];
                $imageOptions['path'] = $img;
                $imageOptions['name'] = $imgFileName;
                $imageOptions['width'] = $newW;
                $imageOptions['height'] = $newH;
                $imageOptions['format'] = $type;

                $images[] = $imageOptions;
            }
        }

        return $images[0];
    }
}