<p align="center">
    <a href="https://github.com/yiisoft" target="_blank">
        <img src="https://github.com/yiisoft.png" height="100px">
    </a>
    <h1 align="center">Yii filesystem</h1>
    <br>
</p>

An abstract filesystem that allows to swap underlying file system without rewriting application code.
Based on [Flysystem](https://flysystem.thephpleague.com/v2/docs/).

[![Latest Stable Version](https://poser.pugx.org/yiisoft/yii-filesystem/v/stable.png)](https://packagist.org/packages/yiisoft/yii-filesystem)
[![Total Downloads](https://poser.pugx.org/yiisoft/yii-filesystem/downloads.png)](https://packagist.org/packages/yiisoft/yii-filesystem)
[![Build status](https://github.com/yiisoft/yii-filesystem/workflows/build/badge.svg)](https://github.com/yiisoft/yii-filesystem/actions?query=workflow%3Abuild)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/yiisoft/yii-filesystem/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/yiisoft/yii-filesystem/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/yiisoft/yii-filesystem/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/yiisoft/yii-filesystem/?branch=master)
[![Mutation testing badge](https://img.shields.io/endpoint?style=flat&url=https%3A%2F%2Fbadge-api.stryker-mutator.io%2Fgithub.com%2Fyiisoft%2Fyii-filesystem%2Fmaster)](https://dashboard.stryker-mutator.io/reports/github.com/yiisoft/yii-filesystem/master)
[![static analysis](https://github.com/yiisoft/yii-filesystem/workflows/static%20analysis/badge.svg)](https://github.com/yiisoft/yii-filesystem/actions?query=workflow%3A%22static+analysis%22)
[![type-coverage](https://shepherd.dev/github/yiisoft/yii-filesystem/coverage.svg)](https://shepherd.dev/github/yiisoft/yii-filesystem)

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

### Unit testing

The package is tested with [PHPUnit](https://phpunit.de/). To run tests:

```shell
./vendor/bin/phpunit
```

### Mutation testing

The package tests are checked with [Infection](https://infection.github.io/) mutation framework. To run it:

```shell
./vendor/bin/infection
```

### Static analysis

The code is statically analyzed with [Psalm](https://psalm.dev/). To run static analysis:

```shell
./vendor/bin/psalm
```

### Support the project

[![Open Collective](https://img.shields.io/badge/Open%20Collective-sponsor-7eadf1?logo=open%20collective&logoColor=7eadf1&labelColor=555555)](https://opencollective.com/yiisoft)

### Follow updates

[![Official website](https://img.shields.io/badge/Powered_by-Yii_Framework-green.svg?style=flat)](https://www.yiiframework.com/)
[![Twitter](https://img.shields.io/badge/twitter-follow-1DA1F2?logo=twitter&logoColor=1DA1F2&labelColor=555555?style=flat)](https://twitter.com/yiiframework)
[![Telegram](https://img.shields.io/badge/telegram-join-1DA1F2?style=flat&logo=telegram)](https://t.me/yii3en)
[![Facebook](https://img.shields.io/badge/facebook-join-1DA1F2?style=flat&logo=facebook&logoColor=ffffff)](https://www.facebook.com/groups/yiitalk)
[![Slack](https://img.shields.io/badge/slack-join-1DA1F2?style=flat&logo=slack)](https://yiiframework.com/go/slack)

## License

The Yii filesystem is free software. It is released under the terms of the BSD License.
Please see [`LICENSE`](./LICENSE.md) for more information.

Maintained by [Yii Software](https://www.yiiframework.com/).
