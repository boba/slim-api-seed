<?php
/**
 * PHP version 7
 *
 * Unit tests for SwaggerRoute
 *
 * @category Test
 * @package  Test\Unit\App\Route
 * @author   Bob Anderson <25436+boba@users.noreply.github.com>
 * @license  http://www.opensource.org/licenses/mit-license.html MIT License
 * @link     https://github.com/boba/slim-api-seed Slim API Seed
 */

namespace Tests\Unit\App\Route;

use App\Route\SwaggerRoute;
use PHPUnit\Framework\TestCase;
use Slim\Http\Environment;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class SwaggerRouteTest
 *
 * @category Test
 * @package  Tests\Unit\App\Route
 * @author   Bob Anderson <25436+boba@users.noreply.github.com>
 * @license  http://www.opensource.org/licenses/mit-license.html MIT License
 * @link     https://github.com/boba/slim-api-seed Slim API Seed
 */
class SwaggerRouteTest extends TestCase
{
    const GET_ENV = ['REQUEST_METHOD' => 'GET', 'REQUEST_URI' => '/swagger'];

    protected $env;
    protected $request;

    /**
     * Test setUp
     *
     * @return void
     */
    public function setUp()
    {
        $this->env = Environment::mock(self::GET_ENV);
        $this->request = Request::createFromEnvironment($this->env);
    }

    /**
     * Test __invoke() response for JSON and 200
     *
     * @covers \App\Route\SwaggerRoute::__invoke
     *
     * @return void
     */
    public function testInvokeResponse()
    {
        $obj = new SwaggerRoute();
        $actual = $obj($this->request, new Response(), ['name' => 'test']);

        $this->assertInstanceOf(Response::class, $actual);
        $this->assertEquals(200, $actual->getStatusCode());
        $this->assertRegExp('/application\/json;\s*charset=utf-8/i', $actual->getHeader('Content-type')[0]);
    }

    /**
     * Test __invoke() swagger information
     *
     * @covers \App\Route\SwaggerRoute::__invoke
     *
     * @return void
     */
    public function testInvokeSwaggerInfo()
    {
        $obj = new SwaggerRoute();
        $res = $obj($this->request, new Response(), []);
        $actual = json_decode($res->getBody(), true);

        $this->assertSame('2.0', $actual['swagger']);
        $this->assertSame('Slim Framework API Seed', $actual['info']['title']);
        $this->assertSame('0.1.0', $actual['info']['version']);
    }

    /**
     * Test __invoke() swagger endpoint content
     *
     * @covers \App\Route\SwaggerRoute::__invoke
     *
     * @return void
     */
    public function testInvokeSwaggerEndpoint()
    {
        $obj = new SwaggerRoute();
        $res = $obj($this->request, new Response(), []);
        $actual = json_decode($res->getBody(), true);
        $this->assertNotNull($actual['paths']['/swagger/swagger.json']);
    }
}
