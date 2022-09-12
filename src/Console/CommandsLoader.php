<?php

declare(strict_types=1);

namespace Symbiotic\Console;

use Psr\Container\ContainerInterface;
use Symbiotic\Apps\AppsRepositoryInterface;
use Symbiotic\Core\Support\Str;
use Symbiotic\Packages\PackagesRepositoryInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\CommandLoader\CommandLoaderInterface;
use Symfony\Component\Console\Exception\CommandNotFoundException;


class CommandsLoader implements CommandLoaderInterface
{

    protected ?array $names = null;

    protected PackagesRepositoryInterface $packages;

    public function __construct(protected ContainerInterface $container)
    {
        $this->packages = $this->container->get(PackagesRepositoryInterface::class);
    }

    public function has(string $name): bool
    {
        $split = Str::sc($name, ':');
        if (is_array($split)) {
            return isset($this->getPackageCommands($split[0])[$name]);
        }

        return false;
    }

    public function getNames(): array
    {
        $names = [];
        foreach ($this->packages->getIds() as $id) {
            $names = array_merge($names, array_keys($this->getPackageCommands($id)));
        }
        $this->names = $names;
        return $this->names;
    }

    protected function getPackageCommands($id): array
    {
        $result = [];
        if ($this->packages->has($id)) {
            $config = $this->packages->getPackageConfig($id);
            $commands = $config->get('commands');
            if (is_array($commands)) {
                foreach ($commands as $name => $class) {
                    $result[$id . ':' . $name] = $class;
                }
            }
        }
        return $result;
    }

    /**
     * @param string $name
     *
     * @return Command
     * @throws
     */
    public function get(string $name): Command
    {
        $package_command = Str::sc($name, ':');
        if (is_array($package_command)) {
            $package_id = $package_command[0];
            $commands = $this->getPackageCommands($package_id);
            if (!isset($commands[$name])) {
                throw new CommandNotFoundException("Command ($name) not found from package ($package_id)!");
            }
            if (!\class_exists($commands[$name])) {
                throw new CommandNotFoundException(
                    "Command class ({$commands[$name]}) is not exists from command [$name]!"
                );
            }
            /**
             * @var AppsRepositoryInterface $appsRepository
             */
            $appsRepository = $this->container->get(AppsRepositoryInterface::class);
            $container = $appsRepository->has($package_command[0])
                ? $appsRepository->getBootedApp($package_id)
                : $this->container;
            // $command = new LazyCommand($name);
            $command = $container->make($commands[$name]);
            $command->setName($name);
            return $command;
        }
        throw new CommandNotFoundException("Command ($name) not found!");
    }

}