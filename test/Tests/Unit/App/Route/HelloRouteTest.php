<?php
/**
 * PHP version 7
 *
 * Unit tests for HelloRoute
 *
 * @category Test
 * @package  Test\Unit\App\Route
 * @author   Bob Anderson <25436+boba@users.noreply.github.com>
 * @license  http://www.opensource.org/licenses/mit-license.html MIT License
 * @link     https://github.com/boba/slim-api-seed Slim API Seed
 */

namespace Tests\Unit\App\Route;

use App\Route\HelloRoute;
use PHPUnit\Framework\TestCase;
use Slim\Http\Environment;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class HelloRouteTest
 *
 * @category Test
 * @package  Tests\Unit\App\Route
 * @author   Bob Anderson <25436+boba@users.noreply.github.com>
 * @license  http://www.opensource.org/licenses/mit-license.html MIT License
 * @link     https://github.com/boba/slim-api-seed Slim API Seed
 */
class HelloRouteTest extends TestCase
{
    const GET_ENV = ['REQUEST_METHOD' => 'POST', 'REQUEST_URI' => '/hello'];

    /**
     * Test __invoke()
     *
     * @covers \App\Route\HelloRoute::__invoke
     *
     * @return void
     */
    public function testInvoke()
    {
        $env = Environment::mock(self::GET_ENV);
        $env['CONTENT_TYPE'] = 'application/json; charset=utf-8';

        $req = Request::createFromEnvironment($env)->withParsedBody(['name' => 'test']);

        $obj = new HelloRoute();
        $actual = $obj($req, new Response(), []);

        $this->assertInstanceOf(Response::class, $actual);
        $this->assertEquals(200, $actual->getStatusCode());
        $this->assertRegExp('/application\/json;\s*charset=utf-8/i', $actual->getHeader('Content-type')[0]);
        $this->assertSame(['error' => false, 'data' => ['hello' => 'test']], json_decode($actual->getBody(), true));
    }

    /**
     * Test __invoke() as JSON request
     *
     * @covers \App\Route\HelloRoute::__invoke
     *
     * @return void
     */
    public function testInvokeJson()
    {
        $env = Environment::mock(self::GET_ENV);
        $env['CONTENT_TYPE'] = 'application/json; charset=utf-8';

        $req = Request::createFromEnvironment($env)->withParsedBody(['name' => 'test', 'format' => 'json']);

        $obj = new HelloRoute();
        $actual = $obj($req, new Response(), []);

        $this->assertInstanceOf(Response::class, $actual);
        $this->assertEquals(200, $actual->getStatusCode());
        $this->assertRegExp('/application\/json;\s*charset=utf-8/i', $actual->getHeader('Content-type')[0]);
        $this->assertSame(['error' => false, 'data' => ['hello' => 'test']], json_decode($actual->getBody(), true));
    }

    /**
     * Test __invoke() with an html request
     *
     * @covers \App\Route\HelloRoute::__invoke
     *
     * @return void
     */
    public function testInvokeHtml()
    {
        $env = Environment::mock(self::GET_ENV);
        $req = Request::createFromEnvironment($env)->withParsedBody(['name' => 'test', 'format' => 'html']);

        $obj = new HelloRoute();
        $actual = $obj($req, new Response(), []);

        $this->assertInstanceOf(Response::class, $actual);
        $this->assertEquals(200, $actual->getStatusCode());
        $this->assertContains('Hello, test.', (string) $actual->getBody());
    }
}
