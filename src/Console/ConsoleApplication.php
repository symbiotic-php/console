<?php

declare(strict_types=1);

namespace Symbiotic\Console;

use Symfony\Component\Console\Application;


class ConsoleApplication extends Application
{

    protected static $logo =
        "
  ____                  _     _       _   _      
 / ___| _   _ _ __ ___ | |__ (_) ___ | |_(_) ___ 
 \___ \| | | | '_ ` _ \| '_ \| |/ _ \| __| |/ __|
  ___) | |_| | | | | | | |_) | | (_) | |_| | (__ 
 |____/ \__, |_| |_| |_|_.__/|_|\___/ \__|_|\___|
        |___/                                    
";

    public function getHelp(): string
    {
        return self::$logo . parent::getHelp();
    }
}