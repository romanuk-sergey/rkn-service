<?php

namespace App\Twig;

use Symfony\Component\VarDumper\VarDumper;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class FakeDebugExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('dump', [$this, 'fakeDump']),
        ];
    }

    public function fakeDump(...$vars)
    {
        return VarDumper::dump(...$vars);
    }
}