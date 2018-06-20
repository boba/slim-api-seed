<?php
/**
 * PHP version 7
 *
 * AppFactory Unit Tests
 *
 * @category Test
 * @package  Tests\Unit\App
 * @author   Bob Anderson <25436+boba@users.noreply.github.com>
 * @license  http://www.opensource.org/licenses/mit-license.html MIT License
 * @link     https://github.com/boba/slim-api-seed Slim API Seed
 */

namespace Tests\Unit\App;

use App\AppFactory;
use App\Dependencies\DependencyDirector;
use App\Route\RouteFactory;
use Monolog\Logger;
use PHPUnit\Framework\TestCase;
use Slim\App;
use Slim\Container;
use Slim\Router;

/**
 * Class AppFactoryTest
 *
 * @category Test
 * @package  Tests\Unit\App
 * @author   Bob Anderson <25436+boba@users.noreply.github.com>
 * @license  http://www.opensource.org/licenses/mit-license.html MIT License
 * @link     https://github.com/boba/slim-api-seed Slim API Seed
 */
class AppFactoryTest extends TestCase
{
    /**
     * Mock container
     *
     * @var Container|mixed
     */
    private $_mockContainer;

    /**
     * Mock director
     *
     * @var DependencyDirector|mixed
     */
    private $_mockDirector;

    /**
     * Mock route factory
     *
     * @var RouteFactory|mixed
     */
    private $_mockRouteFactory;

    /**
     * Test setUp
     *
     * @return void
     */
    public function setUp()
    {
        $this->_mockContainer = $this->createMock(Container::class);
        $this->_mockDirector = $this->createMock(DependencyDirector::class);
        $this->_mockRouteFactory = $this->createMock(RouteFactory::class);
    }

    /**
     * Test __construct() with no arguments creates default container
     *
     * @uses   \Slim\Container::__construct
     * @uses   \App\AppFactory::createContainer
     * @covers \App\AppFactory::__construct
     *
     * @return void
     */
    public function testConstructNoArgsCreatesDefaultContainer()
    {
        $this->assertInstanceOf(Container::class, (new AppFactory())->createContainer());
    }

    /**
     * Test __construct() with no arguments creates default director
     *
     * @uses   \App\Dependencies\DependencyDirector::__construct
     * @uses   \App\AppFactory::createContainer
     * @uses   \App\AppFactory::createDirector
     * @covers \App\AppFactory::__construct
     *
     * @return void
     */
    public function testConstructNoArgsCreatesDefaultDirector()
    {
        $this->assertInstanceOf(DependencyDirector::class, (new AppFactory())->createDirector());
    }

    /**
     * Test __construct with no arguments creates default route factory
     *
     * @uses   \App\AppFactory::createContainer
     * @uses   \App\AppFactory::createRouteFactory
     * @uses   \App\Route\RouteFactory::__construct
     * @covers \App\AppFactory::__construct
     *
     * @return void
     */
    public function testConstructNoArgsCreatesDefaultRouteFactory()
    {
        $this->assertInstanceOf(RouteFactory::class, (new AppFactory())->createRouteFactory());
    }

    /**
     * Test __construct() with container arg
     *
     * @uses   \App\AppFactory::createContainer
     * @covers \App\AppFactory::__construct
     *
     * @return void
     */
    public function testConstructWithContainerArg()
    {
        $this->assertSame($this->_mockContainer, (new AppFactory($this->_mockContainer))->createContainer());
    }

    /**
     * Test __construct() with director arg
     *
     * @uses   \App\AppFactory::createDirector
     * @covers \App\AppFactory::__construct
     *
     * @return void
     */
    public function testConstructWithDirectorArg()
    {
        $this->assertSame($this->_mockDirector, (new AppFactory(null, $this->_mockDirector))->createDirector());
    }

    /**
     * Test __construct() with route factory arg
     *
     * @uses   \App\AppFactory::createRouteFactory
     * @covers \App\AppFactory::__construct
     *
     * @return void
     */
    public function testConstructWithRouteFactoryArg()
    {
        $this->assertSame($this->_mockRouteFactory, (new AppFactory(null, null, $this->_mockRouteFactory))->createRouteFactory());
    }

    /**
     * Test __construct()
     *
     * @uses   \App\AppFactory::createContainer
     * @uses   \App\AppFactory::createDirector
     * @uses   \App\AppFactory::createRouteFactory
     * @covers \App\AppFactory::__construct
     *
     * @return void
     */
    public function testConstruct()
    {
        $obj = new AppFactory($this->_mockContainer, $this->_mockDirector, $this->_mockRouteFactory);
        $this->assertSame($this->_mockContainer, $obj->createContainer());
        $this->assertSame($this->_mockDirector, $obj->createDirector());
        $this->assertSame($this->_mockRouteFactory, $obj->createRouteFactory());
    }

    /**
     * Test setContainer()
     *
     * @uses   \App\AppFactory::__construct
     * @uses   \App\AppFactory::createContainer
     * @covers \App\AppFactory::setContainer
     *
     * @return void
     */
    public function testSetContainer()
    {
        $obj = new AppFactory();
        $obj->setContainer($this->_mockContainer);
        $this->assertSame($this->_mockContainer, $obj->createContainer());
    }

    /**
     * Test setDirector()
     *
     * @uses   \App\AppFactory::__construct
     * @uses   \App\AppFactory::createDirector
     * @covers \App\AppFactory::setDirector
     *
     * @return void
     */
    public function testSetDirector()
    {
        $obj = new AppFactory();
        $obj->setDirector($this->_mockDirector);
        $this->assertSame($this->_mockDirector, $obj->createDirector());
    }

    /**
     * Test setDirector()
     *
     * @uses   \App\AppFactory::__construct
     * @uses   \App\AppFactory::createRouteFactory
     * @covers \App\AppFactory::setRouteFactory()
     *
     * @return void
     */
    public function testSetRouteFactory()
    {
        $obj = new AppFactory();
        $obj->setRouteFactory($this->_mockRouteFactory);
        $this->assertSame($this->_mockRouteFactory, $obj->createRouteFactory());
    }

    /**
     * Test createContainer() with default container
     *
     * @uses   \App\AppFactory::__construct
     * @covers \App\AppFactory::createContainer
     *
     * @return void
     */
    public function testCreateContainerWithDefault()
    {
        $obj = new AppFactory();
        $this->assertInstanceOf(Container::class, $obj->createContainer());
    }

    /**
     * Test createContainer() with injected container
     *
     * @uses   \App\AppFactory::__construct
     * @covers \App\AppFactory::createContainer
     *
     * @return void
     */
    public function testCreateContainerWithInjectedContainer()
    {
        $this->assertSame($this->_mockContainer, (new AppFactory($this->_mockContainer, null))->createContainer());
    }

    /**
     * Test createDirector() with default director
     *
     * @uses   \App\AppFactory::__construct
     * @uses   \App\AppFactory::createContainer
     * @uses   \App\Dependencies\DependencyDirector::__construct
     * @covers \App\AppFactory::createDirector
     *
     * @return void
     */
    public function testCreateDirectorWithDefault()
    {
        $obj = new AppFactory();
        $this->assertInstanceOf(DependencyDirector::class, $obj->createDirector());
    }

    /**
     * Test createDirector() with injected director
     *
     * @uses   \App\AppFactory::__construct
     * @covers \App\AppFactory::createDirector
     *
     * @return void
     */
    public function testCreateDirectorWithInjectedDirector()
    {
        $this->assertSame($this->_mockDirector, (new AppFactory(null, $this->_mockDirector))->createDirector());
    }

    /**
     * Test createRouteFactory() with default route factory
     *
     * @uses   \App\AppFactory::__construct
     * @uses   \App\AppFactory::createContainer
     * @uses   \App\AppFactory::createRouteFactory
     * @uses   \App\Route\RouteFactory::__construct
     * @covers \App\AppFactory::createRouteFactory
     *
     * @return void
     */
    public function testCreateRouteFactoryWithDefault()
    {
        $obj = new AppFactory();
        $this->assertInstanceOf(RouteFactory::class, $obj->createRouteFactory());
    }

    /**
     * Test createRouteFactory() with injected route factory
     *
     * @uses   \App\AppFactory::__construct
     * @covers \App\AppFactory::createRouteFactory
     *
     * @return void
     */
    public function testCreateRouteFactoryWithInjectedRouteFactory()
    {
        $this->assertSame($this->_mockRouteFactory, (new AppFactory(null, null, $this->_mockRouteFactory))->createRouteFactory());
    }

    /**
     * Test createApp()
     *
     * @uses   \App\AppFactory::__construct
     * @uses   \App\AppFactory::createContainer
     * @uses   \App\AppFactory::createDirector
     * @uses   \App\AppFactory::createRouteFactory
     * @covers \App\AppFactory::createApp
     *
     * @return void
     */
    public function testCreateApp()
    {
        $_mockLog = $this->createMock(Logger::class);

        $this->_mockContainer->expects($this->exactly(2))->method('get')
            ->withConsecutive([$this->equalTo('cors_config')])
            ->will(
                $this->returnValueMap(
                    [
                        ['cors_config', []],
                        ['log', $_mockLog]
                    ]
                )
            );

        $this->_mockDirector->expects($this->once())->method('constructContainer');

        $this->_mockRouteFactory->expects($this->once())->method('createDefaultRoutes');
        $this->_mockRouteFactory->expects($this->once())->method('createHelloRoutes');
        $this->_mockRouteFactory->expects($this->once())->method('createSwaggerRoutes');

        $obj = new AppFactory($this->_mockContainer, $this->_mockDirector, $this->_mockRouteFactory);
        $this->assertInstanceOf(App::class, $obj->createApp());
    }
}
