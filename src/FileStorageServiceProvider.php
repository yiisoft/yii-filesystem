<?php

declare(strict_types=1);

namespace Yiisoft\Yii\Filesystem;

use League\Flysystem\FilesystemAdapter;
use Yiisoft\Di\Container;
use Yiisoft\Di\CompositeContainer;
use Psr\Container\ContainerInterface;
use Yiisoft\Di\Contracts\ServiceProviderInterface;
use Yiisoft\Factory\Factory;

final class FileStorageServiceProvider implements ServiceProviderInterface
{
    public function getDefinitions(): array
    {
        return [];
    }

    public function getExtensions(): array
    {
        return [
            ContainerInterface::class => static function (ContainerInterface $container, ContainerInterface $extended) {
                $factory = new Factory();
                $configs = $extended->get(FileStorageConfigs::class)->getConfigs();

                $filesystemsDefinitions = [];
                foreach ($configs as $alias => $config) {
                    $this->validateAdapter($alias, $config);
                    $configParams = $config['config'] ?? [];
                    $aliases = $config['aliases'] ?? [];
                    $adapter = $factory->create($config['adapter']);
                    $filesystemsDefinitions[$alias] = fn() => new Filesystem($adapter, $aliases, $configParams);
                }
                $filesystemsContainer = new Container($filesystemsDefinitions);
                $compositeContainer = new CompositeContainer();
                $compositeContainer->attach($filesystemsContainer);
                $compositeContainer->attach($extended);

                return $compositeContainer;
            }
        ];
    }

    private function validateAdapter(string $alias, array $config): void
    {
        $adapter = $config['adapter']['class'] ?? false;
        if (!$adapter) {
            throw new \RuntimeException("Adapter is not defined in the \"$alias\" storage config.");
        }

        if (!is_subclass_of($adapter, FilesystemAdapter::class)) {
            throw new \RuntimeException('Adapter must implement \League\Flysystem\FilesystemAdapter interface.');
        }
    }
}
