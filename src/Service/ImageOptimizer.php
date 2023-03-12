<?php

namespace App\Service;

use Imagine\Image\Box;
use Imagine\Imagick\Imagine;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ImageOptimizer
{
    public const MAX_WIDTH = 450;
    public const MAX_HEIGHT = 450;

    private Imagine $imagine;

    public function __construct(private readonly ContainerInterface $container)
    {
        $this->imagine = new Imagine();
    }

    public function resize(string $filename): void
    {
        $publicDir = $this->container->getParameter('publicDir');

        $photo = $this->imagine->open($publicDir . $filename);
        $photo->resize(new Box(self::MAX_WIDTH, self::MAX_HEIGHT))->save($publicDir . $filename);
    }
}