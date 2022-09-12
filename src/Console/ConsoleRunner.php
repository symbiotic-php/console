<?php
declare(strict_types=1);

namespace Symbiotic\Console;

use Symbiotic\Core\Runner;


class ConsoleRunner extends Runner
{
    public function isHandle(): bool
    {
        return $this->core['env'] === 'console';
    }

    public function run(): bool
    {
       $consoleApp = new ConsoleApplication();
       $consoleApp->setCommandLoader($this->core->make(CommandsLoader::class));

       return empty($consoleApp->run());
    }


}