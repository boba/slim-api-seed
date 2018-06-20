<?php
/**
 * PHP version 7
 *
 * SessionProcessor tests
 *
 * @category Test
 * @package  Tests\Unit\App\Log
 * @author   Bob Anderson <25436+boba@users.noreply.github.com>
 * @license  http://www.opensource.org/licenses/mit-license.html MIT License
 * @link     https://github.com/boba/slim-api-seed Slim API Seed
 */

namespace Tests\Unit\App\Log;

use App\Log\SessionProcessor;

session_start();

/**
 * Class SessionProcessorTest
 *
 * @category Test
 * @package  Tests\App\Log
 * @author   Bob Anderson <25436+boba@users.noreply.github.com>
 * @license  http://www.opensource.org/licenses/mit-license.html MIT License
 * @link     https://github.com/boba/slim-api-seed Slim API Seed
 */
class SessionProcessorTest extends \PHPUnit\Framework\TestCase
{
    protected $sessionProcessor;

    /**
     * Test setUp
     *
     * @return void
     */
    public function setUp()
    {
        $this->sessionProcessor = new SessionProcessor;
    }

    /**
     * Test session id
     *
     * @covers \App\Log\SessionProcessor::__invoke
     *
     * @return void
     */
    public function testInvokeSessionID()
    {
        $id = session_id();

        $record = [];
        $record = ($this->sessionProcessor)($record);

        $this->assertNotEmpty($record);
        $this->assertNotNull($record['session']);
        $this->assertSame($id, $record['session']);
    }

    /**
     * Test session id
     *
     * @covers \App\Log\SessionProcessor::__invoke
     *
     * @return void
     */
    public function testInvokeRemoteIP()
    {
        $_SERVER['REMOTE_ADDR'] = '127.0.0.2';

        $record = [];
        $record = ($this->sessionProcessor)($record);

        $this->assertNotEmpty($record);
        $this->assertNotNull($record['remoteIP']);
        $this->assertSame('127.0.0.2', $record['remoteIP']);
    }
}
