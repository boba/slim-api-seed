<?php
/**
 * PHP version 7
 *
 * CustomErrorHandler Unit Tests
 *
 * @category Test
 * @package  Tests\Unit\App\Handler
 * @author   Bob Anderson <25436+boba@users.noreply.github.com>
 * @license  http://www.opensource.org/licenses/mit-license.html MIT License
 * @link     https://github.com/boba/slim-api-seed Slim API Seed
 */

namespace Tests\Unit\App\Handler;

use App\Handler\CustomErrorHandler;
use Monolog\Logger;
use PHPUnit\Framework\TestCase;
use Slim\Container;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class CustomErrorHandlerTest
 *
 * @category Test
 * @package  Tests\Unit\App\Handler
 * @author   Bob Anderson <25436+boba@users.noreply.github.com>
 * @license  http://www.opensource.org/licenses/mit-license.html MIT License
 * @link     https://github.com/boba/slim-api-seed Slim API Seed
 */
class CustomErrorHandlerTest extends TestCase
{
    /**
     * Container mock
     *
     * @var Container
     */
    private $_mockContainer;

    /**
     * Request mock
     *
     * @var Request|mixed
     */
    private $_mockRequest;

    /**
     * Response mock
     *
     * @var Response|mixed
     */
    private $_mockResponse;

    /**
     * Logger mock
     *
     * @var Logger|mixed
     */
    private $_mockLog;

    /**
     * Test setUp
     *
     * @return void
     *
     * @throws \ReflectionException
     */
    public function setUp()
    {
        $this->_mockContainer = new Container();
        $this->_mockRequest = $this->createMock(Request::class);
        $this->_mockResponse = $this->createMock(Response::class);
        $this->_mockLog = $this->createMock(Logger::class);
        $this->_mockContainer['log'] = $this->_mockLog;
    }

    /**
     * Test __construct()
     *
     * @covers \App\Handler\CustomErrorHandler::__construct
     *
     * @return void
     */
    public function testConstruct()
    {
        $this->assertInstanceOf(
            CustomErrorHandler::class,
            new CustomErrorHandler($this->_mockContainer)
        );
    }

    /**
     * Test __construct() with null container
     *
     * @covers \App\Handler\CustomErrorHandler::__construct
     *
     * @return void
     */
    public function testConstructNullContainer()
    {
        $this->expectException(\InvalidArgumentException::class);
        new CustomErrorHandler(null);
    }

    /**
     * Test __invoke() returns HTTP 500
     *
     * @uses   \App\Handler\CustomErrorHandler::__construct
     * @covers \App\Handler\CustomErrorHandler::__invoke
     *
     * @return void
     */
    public function testInvokeReturnsHTTP500()
    {
        $res = $this->_mockResponse;
        $req = $this->_mockRequest;

        $res->expects($this->once())->method('withStatus')->with(500)->willReturn($res);
        $res->method('withHeader')->willReturn($res);
        $res->method('withJson')->willReturn($res);

        $obj = new CustomErrorHandler($this->_mockContainer);
        $obj($req, $res, new \Exception('test'));
    }

    /**
     * Test __invoke() returns Content-Type: application/json
     *
     * @uses   \App\Handler\CustomErrorHandler::__construct
     * @covers \App\Handler\CustomErrorHandler::__invoke
     *
     * @return void
     */
    public function testInvokeReturnsJsonContentType()
    {
        $res = $this->_mockResponse;
        $req = $this->_mockRequest;

        $res->method('withStatus')->willReturn($res);
        $res->expects($this->once())->method('withHeader')->with('Content-Type', 'application/json')->willReturn($res);
        $res->method('withJson')->willReturn($res);

        $obj = new CustomErrorHandler($this->_mockContainer);
        $obj($req, $res, new \Exception('test'));
    }

    /**
     * Test __invoke() returns JSON array
     *
     * @uses   \App\Handler\CustomErrorHandler::__construct
     * @covers \App\Handler\CustomErrorHandler::__invoke
     *
     * @return void
     */
    public function testInvokeReturnsJsonArray()
    {
        $res = $this->_mockResponse;
        $req = $this->_mockRequest;

        $res->method('withStatus')->willReturn($res);
        $res->method('withHeader')->willReturn($res);
        $res->expects($this->once())->method('withJson')->with($this->isType('array'))->willReturn($res);

        $obj = new CustomErrorHandler($this->_mockContainer);
        $obj($req, $res, new \Exception('test'));
    }

    /**
     * Test __invoke() returns JSON content
     *
     * @uses   \App\Handler\CustomErrorHandler::__construct
     * @covers \App\Handler\CustomErrorHandler::__invoke
     *
     * @return void
     */
    public function testInvoke()
    {
        $res = new Response();
        $req = $this->_mockRequest;

        $expected = ['error' => true, 'message' => 'Error: test'];

        $obj = new CustomErrorHandler($this->_mockContainer);
        $actual = $obj($req, $res, new \Exception('test'));

        $this->assertSame($expected, json_decode($actual->getBody(), true));
    }
}
