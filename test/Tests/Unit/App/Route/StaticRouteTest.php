<?php
/**
 * PHP version 7
 *
 * Unit tests for StaticRoute
 *
 * @category Test
 * @package  Test\Unit\App\Route
 * @author   Bob Anderson <25436+boba@users.noreply.github.com>
 * @license  http://www.opensource.org/licenses/mit-license.html MIT License
 * @link     https://github.com/boba/slim-api-seed Slim API Seed
 */

namespace Tests\Unit\App\Route;

use App\Route\StaticRoute;
use PHPUnit\Framework\TestCase;
use Slim\Container;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class StaticRouteTest
 *
 * @category Test
 * @package  Tests\Unit\App\Route
 * @author   Bob Anderson <25436+boba@users.noreply.github.com>
 * @license  http://www.opensource.org/licenses/mit-license.html MIT License
 * @link     https://github.com/boba/slim-api-seed Slim API Seed
 */
class StaticRouteTest extends TestCase
{
    protected $container;
    protected $view;

    /**
     * Test setUp()
     *
     * @return void
     */
    public function setUp()
    {
        $this->container = new Container([]);
        $this->view = $this->createMock(\Slim\Views\Twig::class);
        $this->container['view'] = $this->view;
        $this->container['view_cache_status'] = false;
    }

    /**
     * Test __construct()
     *
     * @covers \App\Route\StaticRoute::__construct
     *
     * @return void
     */
    public function testConstruct()
    {
        $obj = new StaticRoute($this->container, 'home');

        $this->assertInstanceOf(StaticRoute::class, $obj);
    }

    /**
     * Test __invoke()
     *
     * @uses   \App\Route\StaticRoute::__construct
     * @covers \App\Route\StaticRoute::__invoke
     *
     * @return void
     */
    public function testInvoke()
    {
        $req = $this->createMock(Request::class);
        $res = $this->createMock(Response::class);

        $this->view->method('render')->with($res, 'home.html', $this->anything())->willReturn($res);

        $obj = new StaticRoute($this->container, 'home');

        $this->assertInstanceOf(Response::class, $obj($req, $res, []));
        $this->assertSame($res, $obj($req, $res, []));
    }
}
