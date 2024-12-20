<?php

namespace PrestaShopCorp\LightweightContainer\Test\ServiceContainer;

use PHPUnit\Framework\TestCase;
use PrestaShopCorp\LightweightContainer\ServiceContainer\Exception\ParameterNotFoundException;
use PrestaShopCorp\LightweightContainer\ServiceContainer\Exception\ServiceNotFoundException;
use PrestaShopCorp\LightweightContainer\ServiceContainer\ServiceContainer;

class ServiceContainerTest extends TestCase
{
    /**
     * @var ServiceContainer
     */
    private $container;

    public function setUp(): void
    {
        $this->container = (new TestServiceContainer(__DIR__ . '/config.php'))->init();
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
    public function itShouldGetParameter()
    {
        $this->assertTrue($this->container->hasParameter('log_level'));
        $this->assertEquals('DEBUG', $this->container->getParameter('log_level'));
    }

    /**
     * @test
     */
    public function itShouldGetParameterDefaultValue()
    {
        $default = 'foo';

        $this->assertFalse($this->container->hasParameter('not_found'));
        $this->assertEquals($default, $this->container->getParameterWithDefault('not_found', $default));
    }

    /**
     * @test
     */
    public function itShouldNotGetParameterDefaultValue()
    {
        $default = 'foo';

        $this->assertTrue($this->container->hasParameter('log_level'));
        $this->assertEquals('DEBUG', $this->container->getParameterWithDefault('log_level', $default));
    }

    /**
     * @test
     */
    public function itShouldSetService()
    {
        $service = new TestService();
        $this->container->set(TestService::class, $service);

        $this->assertTrue($this->container->has(TestService::class));
        $this->assertSame($service, $this->container->get(TestService::class));
    }

    /**
     * @test
     */
    public function itShouldReplaceService()
    {
        $service = new TestService();
        $this->container->set(TestService::class, $service);

        $this->assertTrue($this->container->has(TestService::class));
        $this->assertSame($service, $this->container->get(TestService::class));

        $service2 = new TestService();
        $this->container->set(TestService::class, $service2);

        $this->assertTrue($this->container->has(TestService::class));
        $this->assertSame($service2, $this->container->get(TestService::class));

        $this->assertNotSame($service, $service2);
    }

    /**
     * @test
     */
    public function itShouldInstantiateServiceOnlyOnce()
    {
        $service = $this->createMock(TestService::class);
        $this->container->registerProvider(TestService::class, static function () use ($service) {
            $service->init();

            return $service;
        });

        $this->assertTrue($this->container->hasProvider(TestService::class));

        $service->expects($this->once())->method('init');

        $instance1 = $this->container->get(TestService::class);
        $instance2 = $this->container->get(TestService::class);

        $this->assertTrue($this->container->has(TestService::class));

        $this->assertInstanceOf(TestService::class, $instance1);
        $this->assertInstanceOf(TestService::class, $instance2);

        $this->assertSame($instance1, $instance2);
    }

    /**
     * @test
     */
    public function itShouldInstantiateSingletonService()
    {
        $instance = $this->container->get(TestSingletonService::class);

        $this->assertInstanceOf(TestSingletonService::class, $instance);
        $this->assertTrue($this->container->has(TestSingletonService::class));
    }
}
