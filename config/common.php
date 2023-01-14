<?php

declare(strict_types=1);

use Yiisoft\Aliases\Aliases;
use League\Flysystem\Local\LocalFilesystemAdapter;
use League\Flysystem\UnixVisibility\PortableVisibilityConverter;
use Yiisoft\Yii\Filesystem\FileStorageConfigs;
use Yiisoft\Yii\Filesystem\Filesystem;
use Yiisoft\Yii\Filesystem\FilesystemInterface;

/**
 * @var array $params
 */

return [
    FilesystemInterface::class => static function (Aliases $aliases) use ($params) {
        $aliasesFolder = $aliases->getAll();

        if ($aliasesFolder  === []) {
            throw new \RuntimeException('Alias of the root directory is not defined.');
        }

        $adapter = new LocalFilesystemAdapter(
            $aliases->get('@root'),
            PortableVisibilityConverter::fromArray([
                'file' => [
                    'public' => 0644,
                    'private' => 0600,
                ],
                'dir' => [
                    'public' => 0755,
                    'private' => 0700,
                ],
            ]),
            LOCK_EX,
            LocalFilesystemAdapter::DISALLOW_LINKS
        );

        return new Filesystem($adapter, $aliasesFolder);
    },
    FileStorageConfigs::class => static fn () => new FileStorageConfigs($params['file.storage'] ?? []),
];
