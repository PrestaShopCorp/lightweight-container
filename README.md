# lightweight-container

[![Source Code](https://img.shields.io/badge/source-PrestaShopCorp/lightweight--container-blue.svg?style=flat-square)](https://github.com/PrestaShopCorp/lightweight-container)
[![Latest Version](https://img.shields.io/github/release/PrestaShopCorp/lightweight-container.svg?style=flat-square)](https://github.com/PrestaShopCorp/lightweight-container/releases)
[![Software License](https://img.shields.io/badge/license-OSL-brightgreen.svg?style=flat-square)](https://github.com/PrestaShopCorp/lightweight-container/blob/main/LICENSE)
[![Build Status](https://img.shields.io/github/actions/workflow/status/PrestaShopCorp/lightweight-container/.github/workflows/php.yml?label=CI&logo=github&style=flat-square)](https://github.com/PrestaShopCorp/lightweight-container/actions?query=workflow%3ACI)

[//]: # ([![Total Downloads]&#40;https://img.shields.io/packagist/dt/PrestaShopCorp/lightweight-container.svg?style=flat-square&#41;]&#40;https://packagist.org/packages/prestashopcorp/lightweight-container&#41;)

---

# Context

A module should not bundle and duplicate a heavy dependency from the framework used by the PrestaShop Core, we propose to replace module-lib-service-container with a simple service container to provide the following benefits :

- avoid **collisions** (between modules and with the core);
- avoid **deprecation** for a wider PHP and PrestaShop versions support;
- no more cache construction phase, providers are written directly using PHP;
- php configuration files instead of yaml;
- lighter-weighted module zip (~400 Ko).

The goal is not to replace the symfony container but to enhance stability and compatibility for the ps_accounts module in **single version** mode

Service container should be provided by the Core, and we use it when available (in the ps_accounts case we will start to use symfony controllers for PrestaShop v9 compatibility)

# Implement a Service Container

## Installation & Scoping

[//]: # (```)

[//]: # (composer require prestashopcorp/lightweight-container)

[//]: # (```)

### Configure repository

```json
"repositories": [
    {
        "type": "vcs",
        "url": "git@github.com:PrestaShopCorp/lightweight-container.git"
    }
],
```

### Install package

```shell
  composer require prestashopcorp/lightweight-container:dev-main
```

## Create a configuration file

```php
<?php

return [
    'my_module.a_parameter' => 'my value example',
    'my_module.log_level' => 'ERROR',
];
```

## Init ServiceContainer in your module

```php
/**
 * @return \PrestaShopCorp\LightweightContainer\ServiceContainer\ServiceContainer
 *
 * @throws Exception
 */
public function getServiceContainer()
{
    if (null === $this->container) {
        $this->container = \PrestaShop\Module\PsAccounts\ServiceContainer\ServiceContainer::createInstance(
            __DIR__ . '/config.php'
        );
    }

    return $this->container;
}

/**
 * @param string $serviceName
 *
 * @return mixed
 */
public function getService($serviceName)
{
    return $this->getServiceContainer()->getService($serviceName);
}
```

## Write a ServiceProvider

In the directory `src/ServiceContainer/Provider` of your module sources :

```php
namespace PrestaShop\Module\MyModule\ServiceContainer\Provider;

use PrestaShop\Module\PsAccounts\Service\MyService;
use PrestaShopCorp\LightweightContainer\ServiceContainer\Contract\IServiceProvider;
use PrestaShopCorp\LightweightContainer\ServiceContainer\ServiceContainer;

class SomethingProvider implements IServiceProvider
{
    /**
     * @param ServiceContainer $container
     *
     * @return void
     */
    public function provide(ServiceContainer $container)
    {
        $container->registerProvider(MyService::class, static function () use ($container) {
            return new ServicesBillingClient(
                $container->getParameter('my_module.a_parameter'),
                $container->get(AaService::class),
                $container->get(BbService::class)
            );
        });
    }
}
```

## Register a ServiceProvider

You populate directly the `provides` array of the ServiceContainer class :

```php
namespace PrestaShop\Module\MyModule\ServiceContainer;

use PrestaShopCorp\LightweightContainer\ServiceContainer\ServiceContainer;

class MyModuleServiceContainer extends ServiceContainer
{
    /**
     * @var string[]
     */
    protected $provides = [
        Provider\SomethingProvider::class,
        // ...
    ];
```

# Improvements (next steps)

- Unbind early injected logger (make it a normal service with early injection);
- Add an optional metadata parameter to the `registerProvider` method containing `className` (in case the key doesn’t match a real class name), `singleton` (decide whether its a singleton, true by default), etc …