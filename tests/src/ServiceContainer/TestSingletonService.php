<?php

namespace PrestaShopCorp\LightweightContainer\Test\ServiceContainer;

use PrestaShopCorp\LightweightContainer\ServiceContainer\Contract\ISingletonService;
use PrestaShopCorp\LightweightContainer\ServiceContainer\ServiceContainer;

class TestSingletonService implements ISingletonService
{
    private static $instance;

    public static function getInstance(ServiceContainer $serviceContainer)
    {
        if (self::$instance === null) {
            self::$instance = new TestSingletonService();
        }

        return self::$instance;
    }
}
