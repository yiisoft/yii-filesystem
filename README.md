<p align="center">
    <a href="https://github.com/yiisoft" target="_blank">
        <img src="https://github.com/yiisoft.png" height="100px">
    </a>
    <h1 align="center">Yii filesystem</h1>
    <br>
</p>

An abstract filesystem to manage files and directories. Based on [Flysystem](https://flysystem.thephpleague.com/v2/docs/).

[![Latest Stable Version](https://poser.pugx.org/yiisoft/yii-filesystem/v/stable.png)](https://packagist.org/packages/yiisoft/yii-filesystem)
[![Total Downloads](https://poser.pugx.org/yiisoft/yii-filesystem/downloads.png)](https://packagist.org/packages/yiisoft/yii-filesystem)
[![Build Status](https://travis-ci.com/yiisoft/yii-filesystem.svg?branch=master)](https://travis-ci.com/yiisoft/yii-filesystem)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/yiisoft/yii-filesystem/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/yiisoft/yii-filesystem/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/yiisoft/yii-filesystem/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/yiisoft/yii-filesystem/?branch=master)

## Requirements

The package requires PHP 7.4 and is meant to be used with Yii 3.

## Installation

```
composer require yiisoft/yii-filesystem
```

After installation, the `Yiisoft\Yii\Filesystem\FilesystemInterface` will be automatically registered 
in the main application container. This interface provides a local filesystem with root, as defined in the `@root` alias
of the `aliases` parameter.

## Getting started

The service could be obtained via DI container autowiring:

```php
public function view(\Yiisoft\Yii\Filesystem\FilesystemInterface $filesystem)
{
    $someFileContent = $filesystem->read('/runtime/somefile.txt');
    //...
}
```

Also you can use aliases

```php
$someFileContent = $filesystem->write('@views/site/testfile.txt', 'Test content');
```

## Configuration

Additional filesystems could be configured in `config/params.php` as described below:

```php
'file.storage' => [
    'runtimeStorage' => [
        'adapter' => [
            '__class' => \League\Flysystem\Local\LocalFilesystemAdapter::class,
            '__construct()' => [
                dirname(__DIR__) . '/runtime',
                \League\Flysystem\UnixVisibility\PortableVisibilityConverter::fromArray([
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
                \League\Flysystem\Local\LocalFilesystemAdapter::DISALLOW_LINKS
            ]
        ],
        'aliases' => [
            '@cache' => '@root/cache',
        ]
    ],
    'documentStorage' => [
        'adapter' => [
            '__class' => \League\Flysystem\Local\LocalFilesystemAdapter::class,
            '__construct()' => [
                dirname(__DIR__) . '/docs',
                \League\Flysystem\UnixVisibility\PortableVisibilityConverter::fromArray([
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
                    \League\Flysystem\Local\LocalFilesystemAdapter::DISALLOW_LINKS
                ]
        ],
        'aliases' => [
             '@invoices' => '@root/export/invoices',
             '@orders' => '@root/export/orders',
        ],
    ],
],
```

Aliases `runtimeStorage` and `documentStorage` will be automatically registered in the main application container.
So, you can get it from the container:

```php
public function index(ContainerInterface $container) 
{
    $documentStorage = $container->get('documentStorage');
}
```

If you prefer to use autowiring, you can create own interface for your filesystem.

```php
interface ImageStorageInterface extends \Yiisoft\Yii\Filesystem\FilesystemInterface
{
}
```

And then register it in the `params`:

```php
'file.storage' => [
    ImageStorageInterface::class => [
        'adapter' => [
            '__class' => \League\Flysystem\Local\LocalFilesystemAdapter::class,
            '__construct()' => [
                dirname(__DIR__) . '/storage/images',
                \League\Flysystem\UnixVisibility\PortableVisibilityConverter::fromArray([
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
                \League\Flysystem\Local\LocalFilesystemAdapter::DISALLOW_LINKS
            ]
        ],
    ],
]
```

Now you can use it like this:

```php
//controller action
public function addImage(ImageStorageInterface $imageStorage)
{
    //get image stream...

    $imageStorage->writeStream('/path/to/image/myimage.jpeg', $myImageStream);
}
```

You can find documentation on `FilesystemInterface` methods in the [Flysystem Docs](https://flysystem.thephpleague.com/v2/docs/).  
