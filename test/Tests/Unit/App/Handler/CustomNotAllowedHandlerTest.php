<?php
/**
 * PHP version 7
 *
 * CustomNotAllowedHandler Unit Tests
 *
 * @category Test
 * @package  Tests\Unit\App\Handler
 * @author   Bob Anderson
 * @license  http://www.opensource.org/licenses/mit-license.html MIT License
 * @link     https://github.com/boba/slim-api-seed Slim API Seed
 */

namespace Tests\Unit\App\Handler;

use App\Handler\CustomNotAllowedHandler;
use Monolog\Logger;
use PHPUnit\Framework\TestCase;
use Slim\Container;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class CustomNotAllowedHandlerTest
 *
 * @category Test
 * @package  Tests\Unit\App\Handler
 * @author   Bob Anderson
 * @license  http://www.opensource.org/licenses/mit-license.html MIT License
 * @link     https://github.com/boba/slim-api-seed Slim API Seed
 */
class CustomNotAllowedHandlerTest extends TestCase
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
     * @covers \App\Handler\CustomNotAllowedHandler::__construct
     *
     * @return void
     */
    public function testConstruct()
    {
        $this->assertInstanceOf(
            CustomNotAllowedHandler::class,
            new CustomNotAllowedHandler($this->_mockContainer)
        );
    }

    /**
     * Test __construct() with null container
     *
     * @covers \App\Handler\CustomNotAllowedHandler::__construct
     *
     * @return void
     */
    public function testConstructNullContainer()
    {
        $this->expectException(\InvalidArgumentException::class);
        new CustomNotAllowedHandler(null);
    }

    /**
     * Test __invoke() returns HTTP 405 METHOD NOT ALLOWED
     *
     * @uses   \App\Handler\CustomNotAllowedHandler::__construct
     * @covers \App\Handler\CustomNotAllowedHandler::__invoke
     *
     * @return void
     */
    public function testInvokeReturnsHTTP405()
    {
        $res = $this->_mockResponse;
        $req = $this->_mockRequest;

        $res->expects($this->once())->method('withStatus')->with(405)->willReturn($res);
        $res->method('withHeader')->willReturn($res);
        $res->method('withJson')->willReturn($res);

        $obj = new CustomNotAllowedHandler($this->_mockContainer);
        $obj($req, $res, []);
    }

    /**
     * Test __invoke() returns empty Allow header
     *
     * @uses   \App\Handler\CustomNotAllowedHandler::__construct
     * @covers \App\Handler\CustomNotAllowedHandler::__invoke
     *
     * @return void
     */
    public function testInvokeReturnsEmptyAllowHeader()
    {
        $res = $this->_mockResponse;
        $req = $this->_mockRequest;

        $res->method('withStatus')->willReturn($res);
        $res->expects($this->exactly(2))->method('withHeader')
            ->withConsecutive(['Allow', ''], ['Content-Type', 'application/json'])->willReturn($res);
        $res->method('withJson')->willReturn($res);

        $obj = new CustomNotAllowedHandler($this->_mockContainer);
        $obj($req, $res, ['']);
    }

    /**
     * Test __invoke() returns single Allow header
     *
     * @uses   \App\Handler\CustomNotAllowedHandler::__construct
     * @covers \App\Handler\CustomNotAllowedHandler::__invoke
     *
     * @return void
     */
    public function testInvokeReturnsSingleAllowHeader()
    {
        $res = $this->_mockResponse;
        $req = $this->_mockRequest;

        $res->method('withStatus')->willReturn($res);
        $res->expects($this->exactly(2))->method('withHeader')
            ->withConsecutive(['Allow', 'GET'], ['Content-Type', 'application/json'])->willReturn($res);
        $res->method('withJson')->willReturn($res);

        $obj = new CustomNotAllowedHandler($this->_mockContainer);
        $obj($req, $res, ['GET']);
    }

    /**
     * Test __invoke() returns multiple Allow headers
     *
     * @uses   \App\Handler\CustomNotAllowedHandler::__construct
     * @covers \App\Handler\CustomNotAllowedHandler::__invoke
     *
     * @return void
     */
    public function testInvokeReturnsMultipleAllowHeaders()
    {
        $res = $this->_mockResponse;
        $req = $this->_mockRequest;

        $res->method('withStatus')->willReturn($res);
        $res->expects($this->exactly(2))->method('withHeader')
            ->withConsecutive(['Allow', 'GET, POST'], ['Content-Type', 'application/json'])->willReturn($res);
        $res->method('withJson')->willReturn($res);

        $obj = new CustomNotAllowedHandler($this->_mockContainer);
        $obj($req, $res, ['GET', 'POST']);
    }

    /**
     * Test __invoke() returns JSON array
     *
     * @uses   \App\Handler\CustomNotAllowedHandler::__construct
     * @covers \App\Handler\CustomNotAllowedHandler::__invoke
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

        $obj = new CustomNotAllowedHandler($this->_mockContainer);
        $obj($req, $res, []);
    }

    /**
     * Test __invoke() returns JSON content
     *
     * @uses   \App\Handler\CustomNotAllowedHandler::__construct
     * @covers \App\Handler\CustomNotAllowedHandler::__invoke
     *
     * @return void
     */
    public function testInvoke()
    {
        $res = new Response();
        $req = $this->_mockRequest;

        $expected = ['error' => true, 'message' => 'Method must be one of: GET, POST'];

        $obj = new CustomNotAllowedHandler($this->_mockContainer);
        $actual = $obj($req, $res, ['GET', 'POST']);

        $this->assertSame($expected, json_decode($actual->getBody(), true));
    }
}
