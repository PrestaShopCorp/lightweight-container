<?php

namespace PrestaShopCorp\LightweightContainer\Test\ServiceContainer;

use PrestaShopCorp\LightweightContainer\ServiceContainer\ServiceContainer;

class TestServiceContainer extends ServiceContainer
{
    public function getLogger()
    {
        return new TestLogger();
    }
}
