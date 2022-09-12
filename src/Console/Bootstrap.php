<?php

declare(strict_types=1);

namespace Symbiotic\Console;

use Symbiotic\Container\DIContainerInterface;
use Symbiotic\Core\AbstractBootstrap;


class Bootstrap extends AbstractBootstrap
{
    public function bootstrap(DIContainerInterface $core): void
    {
        $core->addRunner(new ConsoleRunner($core));
    }
}