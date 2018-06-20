<?php
/**
 * PHP version 7
 *
 * Home endpoint tests
 *
 * @category Tests
 * @package  Tests\Endpoints
 * @author   Bob Anderson <25436+boba@users.noreply.github.com>
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
 * Class HomeTest
 *
 * @category Test
 * @package  Test
 * @author   Bob Anderson <25436+boba@users.noreply.github.com>
 * @license  http://www.opensource.org/licenses/mit-license.html MIT License
 * @link     https://github.com/boba/slim-api-seed Slim API Seed
 */
class HomeTest extends \PHPUnit\Framework\TestCase
{
    const TEST_SETTINGS = [
        'defaults' => [
            'cache_dir' => '../../test/cache',
            'template_dir' => 'src/template',
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
     * Test /home default format (HTML)
     *
     * @coversNothing
     *
     * @return void
     */
    public function testHomeGetDefault()
    {
        $env = Environment::mock(
            [
                'REQUEST_METHOD' => 'GET',
                'REQUEST_URI' => '/home'
            ]
        );

        $req = Request::createFromEnvironment($env);
        $this->app->getContainer()['request'] = $req;

        $response = $this->app->run(true);

        $this->assertInstanceOf('\Slim\Http\Response', $response);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertRegExp(
            '/text\/html;\s*charset=utf-8/i',
            $response->getHeader('Content-type')[0]
        );

        $this->assertNotNull($response->getBody());
    }
}
