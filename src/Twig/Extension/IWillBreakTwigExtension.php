<?php

namespace App\Twig\Extension;

use Twig\Extension\AbstractExtension;

class IWillBreakTwigExtension extends AbstractExtension
{
    public function __construct(
    ) {
        throw new \RuntimeException('I Broke twig but partially :(!');
    }
}
