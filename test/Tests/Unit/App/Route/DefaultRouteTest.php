<?php
/**
 * PHP version 7
 *
 * Unit tests for DefaultRoute
 *
 * @category Test
 * @package  Test\Unit\App\Route
 * @author   Bob Anderson
 * @license  http://www.opensource.org/licenses/mit-license.html MIT License
 * @link     https://github.com/boba/slim-api-seed Slim API Seed
 */

namespace Tests\Unit\App\Route;

use App\Route\DefaultRoute;
use PHPUnit\Framework\TestCase;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class DefaultRouteTest
 *
 * @category Test
 * @package  Tests\Unit\App\Route
 * @author   Bob Anderson
 * @license  http://www.opensource.org/licenses/mit-license.html MIT License
 * @link     https://github.com/boba/slim-api-seed Slim API Seed
 */
class DefaultRouteTest extends TestCase
{
    /**
     * Test __invoke()
     *
     * @covers \App\Route\DefaultRoute::__invoke
     *
     * @return void
     */
    public function testInvoke()
    {
        $req = $this->createMock(Request::class);
        $res = $this->createMock(Response::class);

        $obj = new DefaultRoute();

        $this->assertInstanceOf(Response::class, $obj($req, $res, []));
        $this->assertSame($res, $obj($req, $res, []));
    }
}
