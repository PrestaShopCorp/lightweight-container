<?php

namespace PrestaShopCorp\LightweightContainer\Test\ServiceContainer;

use PHPUnit\Framework\TestCase;
use PrestaShopCorp\LightweightContainer\ServiceContainer\Exception\ParameterNotFoundException;
use PrestaShopCorp\LightweightContainer\ServiceContainer\Exception\ServiceNotFoundException;
use PrestaShopCorp\LightweightContainer\ServiceContainer\ServiceContainer;

class TestService
{
    public function init()
    {
        // empty method
    }
}

class ServiceContainerTest extends TestCase
{
    /**
     * @var ServiceContainer
     */
    private $container;

    public function setUp(): void
    {
        $this->container = TestServiceContainer::createInstance(__DIR__ . '/config.php');
    }

    /**
     * @test
     */
    public function itShouldThrowServiceNotFoundExceptionIfProviderIsMissing()
    {
        $this->expectException(ServiceNotFoundException::class);

        $this->container->get(TestService::class);
    }

    /**
     * @test
     */
    public function itShouldThrowParameterNotFoundExceptionIfParameterNotFound()
    {
        $this->expectException(ParameterNotFoundException::class);

        $this->container->getParameter('foo.bar');
    }

    /**
     * @test
     */
    public function itShouldGetConfigurationParameter()
    {
        $this->assertEquals('DEBUG', $this->container->getParameter('log_level'));
    }

    /**
     * @test
     */
    public function itShouldInstantiateServiceOnlyOnce()
    {
        $service = $this->createMock(TestService::class);
        $service->expects($this->once())->method('init');

        $this->container->registerProvider(TestService::class, static function () use ($service) {
            $service->init();

            return $service;
        });

        $this->assertTrue($this->container->hasProvider(TestService::class));

        $i = 0;
        while ($i++ < 3) {
            $this->assertInstanceOf(TestService::class, $this->container->get(TestService::class));
        }
    }
}
