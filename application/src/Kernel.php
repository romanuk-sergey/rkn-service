<?php

namespace App;

use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;

class Kernel extends BaseKernel
{
    public function boot(): void
    {
        parent::boot();
        date_default_timezone_set("Europe/Moscow");
    }

    use MicroKernelTrait;
}
