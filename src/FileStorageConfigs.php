<?php

declare(strict_types=1);

namespace Yiisoft\Yii\Filesystem;

final class FileStorageConfigs
{
    public function __construct(private array $storageConfigs)
    {
    }

    public function getConfigs(): array
    {
        return $this->storageConfigs;
    }
}
