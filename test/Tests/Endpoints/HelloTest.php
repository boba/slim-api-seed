<?php
/**
 * PHP version 7
 *
 * Hello endpoint tests
 *
 * @category Tests
 * @package  Tests\Endpoints
 * @author   Bob Anderson
 * @license  http://www.opensource.org/licenses/mit-license.html MIT License
 * @link     https://github.com/boba/slim-api-seed Slim API Seed
 */

namespace Tests\Endpoints;

use App\AppFactory;
use Monolog\Logger;
use Slim\Container;
use Slim\Http\Environment;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class HelloTest
 *
 * @category Test
 * @package  Test
 * @author   Bob Anderson
 * @license  http://www.opensource.org/licenses/mit-license.html MIT License
 * @link     https://github.com/boba/slim-api-seed Slim API Seed
 */
class HelloTest extends \PHPUnit\Framework\TestCase
{
    const TEST_SETTINGS = [
        'defaults' => [
            'logger' => '',
            'logger_name' => 'API_LOGGER',
            'log_dir' => '../../test/logs',
            'log_file' => 'test.log',
            'log_signal' => Logger::DEBUG, // debug,info,notice,warning,critical
            'ini_name' => 'test/test.ini',
            'displayErrorDetails' => true
        ]
    ];

    /**
     * Slim Application
     *
     * @var \Slim\App
     */
    protected $app;

    /**
     * Test setUp
     *
     * @return void
     */
    public function setUp()
    {
        $container = new Container($this::TEST_SETTINGS);
        $this->app = (new AppFactory($container))->createApp();

        // did bootstrapping work?
        assert(isset($this->app) && $this->app instanceof \Slim\App);
    }

    /**
     * Test /hello/foo?format=html
     *
     * @coversNothing
     *
     * @return void
     */
    public function testHelloGetHTMLFormat()
    {
        $env = Environment::mock(
            [
                'REQUEST_METHOD' => 'GET',
                'REQUEST_URI' => '/hello/foo',
                'QUERY_STRING' => 'format=html'
            ]
        );

        $req = Request::createFromEnvironment($env);
        $this->app->getContainer()['request'] = $req;

        $response = $this->app->run(true);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertRegExp(
            '/text\/html;\s*charset=utf-8/i',
            $response->getHeader('Content-type')[0]
        );

        $expected = '<p id="hello">Hello, foo.</p>';
        $this->assertSame((string)$response->getBody(), $expected);
    }

    /**
     * Test /hello/foo?format=json
     *
     * @coversNothing
     *
     * @return void
     */
    public function testHelloGetJsonFormat()
    {
        /**
         * Response object
         *
         * @var Response
         */
        $response = null;

        $env = Environment::mock(
            [
                'REQUEST_METHOD' => 'GET',
                'REQUEST_URI' => '/hello/foo',
                'QUERY_STRING' => 'format=json'
            ]
        );

        $req = Request::createFromEnvironment($env);
        $this->app->getContainer()['request'] = $req;

        $response = $this->app->run(true);

        $this->assertInstanceOf('\Slim\Http\Response', $response);
        $this->assertSame(200, $response->getStatusCode());
        $this->assertRegExp(
            '/application\/json;\s*charset=utf-8/i',
            $response->getHeader('Content-type')[0]
        );

        $actual = json_decode($response->getBody(), true);

        $this->assertNotNull($actual['data']);
        $this->assertNotNull($actual['data']['hello']);
        $this->assertSame('foo', $actual['data']['hello']);
    }

    /**
     * Test /hello/foo default format (JSON)
     *
     * @coversNothing
     *
     * @return void
     */
    public function testHelloGetDefault()
    {
        $env = Environment::mock(
            [
                'REQUEST_METHOD' => 'GET',
                'REQUEST_URI' => '/hello/foo'
            ]
        );

        $req = Request::createFromEnvironment($env);
        $this->app->getContainer()['request'] = $req;

        $response = $this->app->run(true);

        $this->assertInstanceOf('\Slim\Http\Response', $response);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertRegExp(
            '/application\/json;\s*charset=utf-8/i',
            $response->getHeader('Content-type')[0]
        );

        $actual = json_decode($response->getBody(), true);

        $expected = ['error' => false, 'data' => ['hello' => 'foo']];
        $this->assertSame($expected, $actual);

        $this->assertNotNull($response->getBody());
        $this->assertNotNull($actual);
        $this->assertNotNull($actual['data']);
        $this->assertNotNull($actual['data']['hello']);
        $this->assertSame('foo', $actual['data']['hello']);
    }
}
