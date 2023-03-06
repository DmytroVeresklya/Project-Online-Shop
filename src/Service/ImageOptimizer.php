<?php

namespace App\Service;

use Imagine\Image\Box;
use Imagine\Imagick\Imagine;

class ImageOptimizer
{
    public const MAX_WIDTH = 450;
    public const MAX_HEIGHT = 450;

    private $imagine;

    public function __construct()
    {
        $this->imagine = new Imagine();
    }

    public function resize(string $filename): void
    {
        $width  = self::MAX_WIDTH;
        $height = self::MAX_HEIGHT;

        $photo = $this->imagine->open('/var/www/dogmarket/public' . $filename);
        $photo->resize(new Box($width, $height))->save('/var/www/dogmarket/public' . $filename);
    }
}