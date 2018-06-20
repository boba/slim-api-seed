<?php
/**
 * PHP version 7
 *
 * DependencyDirector Unit Tests
 *
 * @category Test
 * @package  Tests\Unit\App\Dependencies
 * @author   Bob Anderson
 * @license  http://www.opensource.org/licenses/mit-license.html MIT License
 * @link     https://github.com/boba/slim-api-seed Slim API Seed
 */

namespace Tests\Unit\App\Dependencies;

use App\Dependencies\ContainerBuilder;
use App\Dependencies\DependencyDirector;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

/**
 * Class DependencyDirectorTest
 *
 * @category Test
 * @package  Tests\Unit\App\Dependencies
 * @author   Bob Anderson
 * @license  http://www.opensource.org/licenses/mit-license.html MIT License
 * @link     https://github.com/boba/slim-api-seed Slim API Seed
 */
class DependencyDirectorTest extends TestCase
{
    /**
     * Container mock
     *
     * @var ContainerInterface|mixed
     */
    private $_mockContainer;

    /**
     * Builder mock
     *
     * @var ContainerBuilder|mixed
     */
    private $_mockBuilder;

    /**
     * Test setUp
     *
     * @return void
     *
     * @throws \ReflectionException
     */
    public function setUp()
    {
        $this->_mockContainer = $this->createMock(ContainerInterface::class);
        $this->_mockBuilder = $this->createMock(ContainerBuilder::class);
    }

    /**
     * Test constructor
     *
     * @covers \App\Dependencies\DependencyDirector::__construct
     *
     * @throws \ReflectionException
     *
     * @return void
     */
    public function testConstruct()
    {
        $this->assertInstanceOf(
            DependencyDirector::class,
            new DependencyDirector($this->_mockContainer)
        );
    }

    /**
     * Test constructor with null container
     *
     * @covers \App\Dependencies\DependencyDirector::__construct
     *
     * @return void
     */
    public function testConstructNullContainer()
    {
        $this->expectException(\InvalidArgumentException::class);
        new DependencyDirector(null);
    }

    /**
     * Test getBuilder() when a builder has not been injected
     *
     * @uses   \App\Dependencies\ContainerBuilder::__construct
     * @uses   \App\Dependencies\ContainerBuilder::getContainer
     * @uses   \App\Dependencies\DependencyDirector::__construct
     * @covers \App\Dependencies\DependencyDirector::getBuilder
     *
     * @return void
     *
     * @throws \ReflectionException
     */
    public function testGetBuilderWithNullBuilder()
    {
        $obj = new DependencyDirector($this->_mockContainer);

        $reflection = new \ReflectionClass((get_class($obj)));
        $method = $reflection->getMethod('getBuilder');
        $method->setAccessible(true);

        $builder = $method->invokeArgs($obj, []);

        $this->assertNotNull($builder);
        $this->assertInstanceOf(ContainerBuilder::class, $builder);
        $this->assertInstanceOf(ContainerInterface::class, $builder->getContainer());
        $this->assertSame($this->_mockContainer, $builder->getContainer());
    }

    /**
     * Test setBuilder()
     *
     * @uses   \App\Dependencies\DependencyDirector::__construct
     * @uses   \App\Dependencies\DependencyDirector::getBuilder
     * @covers \App\Dependencies\DependencyDirector::setBuilder
     *
     * @throws \ReflectionException
     *
     * @return void
     */
    public function testSetBuilder()
    {
        $obj = new DependencyDirector($this->_mockContainer);
        $obj->setBuilder($this->_mockBuilder);

        $reflection = new \ReflectionClass((get_class($obj)));
        $method = $reflection->getMethod('getBuilder');
        $method->setAccessible(true);

        $actual = $method->invokeArgs($obj, []);

        $this->assertNotNull($actual);
        $this->assertInstanceOf(ContainerBuilder::class, $actual);
        $this->assertSame($this->_mockBuilder, $actual);
    }

    /**
     * Test constructContainer()
     *
     * @uses   \App\Dependencies\DependencyDirector::__construct
     * @uses   \App\Dependencies\DependencyDirector::getBuilder
     * @uses   \App\Dependencies\DependencyDirector::getContainer
     * @uses   \App\Dependencies\DependencyDirector::setBuilder
     * @covers \App\Dependencies\DependencyDirector::constructContainer
     *
     * @return void
     *
     * @throws \ReflectionException
     */
    public function testConstructContainer()
    {
        $b = $this->_mockBuilder;
        $b->expects($this->once())->method('buildIniDependencies');
        $b->expects($this->once())->method('buildApplicationDependencies');
        $b->expects($this->once())->method('buildLoggingDependencies');
        $b->expects($this->once())->method('buildErrorHandlerDependencies');
        $b->expects($this->once())->method('buildCORSDependencies');

        $obj = new DependencyDirector($this->_mockContainer);
        $obj->setBuilder($b);

        $obj->constructContainer();
    }

    /**
     * Test getContainer()
     *
     * @uses   \App\Dependencies\DependencyDirector::__construct
     * @covers \App\Dependencies\DependencyDirector::getContainer
     *
     * @return void
     */
    public function testGetContainer()
    {
        $obj = new DependencyDirector($this->_mockContainer);
        $this->assertSame($this->_mockContainer, $obj->getContainer());
    }
}
