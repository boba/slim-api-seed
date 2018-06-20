<?php
/**
 * PHP version 7
 *
 * Unit tests for HelloNameRoute
 *
 * @category Test
 * @package  Test\Unit\App\Route
 * @author   Bob Anderson <25436+boba@users.noreply.github.com>
 * @license  http://www.opensource.org/licenses/mit-license.html MIT License
 * @link     https://github.com/boba/slim-api-seed Slim API Seed
 */

namespace Tests\Unit\App\Route;

use App\Route\HelloNameRoute;
use PHPUnit\Framework\TestCase;
use Slim\Http\Environment;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class HelloNameRouteTest
 *
 * @category Test
 * @package  Tests\Unit\App\Route
 * @author   Bob Anderson <25436+boba@users.noreply.github.com>
 * @license  http://www.opensource.org/licenses/mit-license.html MIT License
 * @link     https://github.com/boba/slim-api-seed Slim API Seed
 */
class HelloNameRouteTest extends TestCase
{
    const GET_ENV = ['REQUEST_METHOD' => 'GET', 'REQUEST_URI' => '/hello/foo'];

    /**
     * Test __invoke()
     *
     * @covers \App\Route\HelloNameRoute::__invoke
     *
     * @return void
     */
    public function testInvoke()
    {
        $env = Environment::mock(self::GET_ENV);
        $req = Request::createFromEnvironment($env);

        $res = new Response();

        $obj = new HelloNameRoute();
        $actual = $obj($req, $res, ['name' => 'test']);

        $this->assertInstanceOf(Response::class, $actual);
        $this->assertEquals(200, $actual->getStatusCode());
        $this->assertRegExp('/application\/json;\s*charset=utf-8/i', $actual->getHeader('Content-type')[0]);
        $this->assertSame(['error' => false, 'data' => ['hello' => 'test']], json_decode($actual->getBody(), true));
    }

    /**
     * Test __invoke() as JSON request
     *
     * @covers \App\Route\HelloNameRoute::__invoke
     *
     * @return void
     */
    public function testInvokeJson()
    {
        $env = Environment::mock(self::GET_ENV);
        $req = Request::createFromEnvironment($env)->withQueryParams(['format' => 'json']);

        $res = new Response();

        $obj = new HelloNameRoute();
        $actual = $obj($req, $res, ['name' => 'test']);

        $this->assertInstanceOf(Response::class, $actual);
        $this->assertEquals(200, $actual->getStatusCode());
        $this->assertRegExp('/application\/json;\s*charset=utf-8/i', $actual->getHeader('Content-type')[0]);
        $this->assertSame(['error' => false, 'data' => ['hello' => 'test']], json_decode($actual->getBody(), true));
    }

    /**
     * Test __invoke() with an html request
     *
     * @covers \App\Route\HelloNameRoute::__invoke
     *
     * @return void
     */
    public function testInvokeHtml()
    {
        $env = Environment::mock(self::GET_ENV);
        $req = Request::createFromEnvironment($env)->withQueryParams(['format' => 'html']);

        $res = new Response();

        $obj = new HelloNameRoute();
        $actual = $obj($req, $res, ['name' => 'test']);

        $this->assertInstanceOf(Response::class, $actual);
        $this->assertEquals(200, $actual->getStatusCode());
        $this->assertContains('Hello, test.', (string) $actual->getBody());
    }
}
