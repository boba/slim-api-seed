<?php
/**
 * PHP version 7
 *
 * RouteFactory Unit Tests
 *
 * @category Test
 * @package  Test\Unit\Route
 * @author   Bob Anderson
 * @license  http://www.opensource.org/licenses/mit-license.html MIT License
 * @link     https://github.com/boba/slim-api-seed Slim API Seed
 */

namespace Tests\Unit\App\Route;

use App\Route\RouteFactory;
use PHPUnit\Framework\TestCase;
use Slim\App;

/**
 * Class RouteFactoryTest
 *
 * @category Test
 * @package  Tests\Unit\App\Route
 * @author   Bob Anderson
 * @license  http://www.opensource.org/licenses/mit-license.html MIT License
 * @link     https://github.com/boba/slim-api-seed Slim API Seed
 */
class RouteFactoryTest extends TestCase
{

    private $_mockApp;

    /**
     * Test setUp
     *
     * @return void
     */
    public function setUp()
    {
        $this->_mockApp = $this->createMock(App::class);
    }

    /**
     * Test __construct()
     *
     * @covers \App\Route\RouteFactory::__construct
     *
     * @return void
     */
    public function testConstruct()
    {
        $obj = new RouteFactory();
        $this->assertInstanceOf(RouteFactory::class, $obj);
    }

    /**
     * Test createDefaultRoute()
     *
     * @uses   \App\Route\RouteFactory::__construct
     * @covers \App\Route\RouteFactory::createDefaultRoutes
     *
     * @return void
     */
    public function testCreateDefaultRoutes()
    {
        $this->_mockApp->expects($this->once())->method('get')->with($this->equalTo('/'), $this->anything());

        (new RouteFactory())->createDefaultRoutes($this->_mockApp);
    }

    /**
     * Test createHelloRoute()
     *
     * @uses   \App\Route\RouteFactory::__construct
     * @covers \App\Route\RouteFactory::createHelloRoutes
     *
     * @return void
     */
    public function testCreateHelloRoutes()
    {
        $this->_mockApp->expects($this->once())->method('get')->with($this->equalTo('/hello/{name}'), $this->anything());
        $this->_mockApp->expects($this->once())->method('post')->with($this->equalTo('/hello'), $this->anything());

        (new RouteFactory())->createHelloRoutes($this->_mockApp);
    }

    /**
     * Test createSwaggerRoute()
     *
     * @uses   \App\Route\RouteFactory::__construct
     * @covers \App\Route\RouteFactory::createSwaggerRoutes
     *
     * @return void
     */
    public function testCreateSwaggerRoutes()
    {
        $this->_mockApp->expects($this->once())->method('get')->with($this->equalTo('/swagger/swagger.json'), $this->anything());

        (new RouteFactory())->createSwaggerRoutes($this->_mockApp);
        $this->assertTrue(true);
    }
}
