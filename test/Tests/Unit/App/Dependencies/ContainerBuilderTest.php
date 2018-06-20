<?php
/**
 * PHP version 7
 *
 * ContainerBuilder Unit Tests
 *
 * @category Test
 * @package  Tests\Unit\App\Dependencies
 * @author   Bob Anderson <25436+boba@users.noreply.github.com>
 * @license  http://www.opensource.org/licenses/mit-license.html MIT License
 * @link     https://github.com/boba/slim-api-seed Slim API Seed
 */

namespace Tests\Unit\App\Dependencies;

use App\Dependencies\ContainerBuilder;
use App\Handler\CustomErrorHandler;
use App\Handler\CustomNotAllowedHandler;
use App\Handler\CustomNotFoundHandler;
use App\Handler\CustomPHPErrorHandler;
use Monolog\Logger;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use phpmock\MockEnabledException;
use phpmock\spy\Spy;
use PHPUnit\Framework\TestCase;
use Pimple\Exception\UnknownIdentifierException;
use Psr\Container\ContainerInterface;
use Slim\Container;
use Slim\Handlers\Error;
use Slim\Handlers\NotAllowed;
use Slim\Handlers\NotFound;
use Slim\Handlers\PhpError;

/**
 * Class ContainerBuilderTest
 *
 * @category Test
 * @package  Tests\Unit\App\Dependencies
 * @author   Bob Anderson <25436+boba@users.noreply.github.com>
 * @license  http://www.opensource.org/licenses/mit-license.html MIT License
 * @link     https://github.com/boba/slim-api-seed Slim API Seed
 */
class ContainerBuilderTest extends TestCase
{
    /**
     * CORS URLs for testing
     */
    const CORS_URLS = [
        'http://a.test:80',
        'https://a.test:443',
        'https://example.com'
    ];

    /**
     * Test log folder
     */
    const VFS_DIR = './test/';

    /**
     * Contents of a test.ini file
     */
    const INI_FILE_CONTENTS = <<<EOC
[API]
API_URL=http://localhost:8000/
LogPath=/app/test/logs
LogThreshold=100
CORS_URLs=http://a.test:80,https://a.test:443,https://example.com
EOC;

    /**
     * Virtual test folder structure
     */
    const VFS_STRUCTURE = [
        'ini' => [
            'invalid.ini' => "I'm so bad I should be in detention",
            'valid.ini' => "[section]\nkey=value\n",
            'test.ini' => self::INI_FILE_CONTENTS
        ]
    ];

    /**
     * Default app settings
     */
    const TEST_SETTINGS = [
        'log_dir' => './test/logs',
        'log_signal' => Logger::DEBUG,
        'logger_name' => 'TEST',
        'log_file' => 'test.log',
        'ini_name' => 'test.ini'
    ];

    /**
     * Empty .ini file settings
     */
    const TEST_EMPTY_INI = [];

    /**
     * Object under test
     *
     * @var ContainerBuilder|mixed
     */
    private $_builder;

    /**
     * Spy error_log
     *
     * @var Spy
     */
    private $_spyErrorLog;

    /**
     * DI container
     *
     * @var ContainerInterface
     */
    private $_c;

    /**
     * Virtual filesystem root directory
     *
     * @var vfsStreamDirectory
     */
    private $_root;

    /**
     * Test setUp
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();

        $this->_root = vfsStream::setup('test_dir', null, self::VFS_STRUCTURE);

        $this->_c = new Container([]);

        // stub createLogDir() to skip actual directory creation for testing
        $this->_builder = $this->getMockBuilder(ContainerBuilder::class)
            ->setConstructorArgs([$this->_c])
            ->setMethods(['createLogDir'])
            ->getMock();
        $this->_builder->method('createLogDir')
            ->willReturn($this->returnValue(true));

        $this->_spyErrorLog = new Spy(
            'App\Dependencies',
            "error_log",
            function () {
                return;
            }
        );
    }

    /**
     * Test tearDown
     *
     * @return void
     */
    public function tearDown()
    {
        $this->_spyErrorLog->disable();
        parent::tearDown();
    }

    /**
     * Test constructor
     *
     * @uses   \App\Dependencies\ContainerBuilder::getContainer
     * @covers \App\Dependencies\ContainerBuilder::__construct
     *
     * @return void
     */
    public function testConstruct()
    {
        $this->_c['foo'] = 'test';

        $this->assertNotNull($this->_builder);
        $this->assertInstanceOf(ContainerBuilder::class, $this->_builder);
        $this->assertSame($this->_c, $this->_builder->getContainer());
        $this->assertEquals('test', $this->_builder->getContainer()['foo']);
    }

    /**
     * Test constructor with null container
     *
     * @covers \App\Dependencies\ContainerBuilder::__construct
     *
     * @return void
     */
    public function testConstructNullContainer()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->_builder = new ContainerBuilder(null);
    }

    /**
     * Test constructor with unconfigured container does not contain ini
     *
     * @covers \App\Dependencies\ContainerBuilder::__construct
     *
     * @return void
     */
    public function testConstructUnconfiguredIni()
    {
        $this->expectException(UnknownIdentifierException::class);
        $this->assertNull($this->_c['ini']);
    }

    /**
     * Test constructor with unconfigured container does not contain api
     *
     * @covers \App\Dependencies\ContainerBuilder::__construct
     *
     * @return void
     */
    public function testConstructUnconfiguredApi()
    {
        $this->expectException(UnknownIdentifierException::class);
        $this->assertNull($this->_c['api']);
    }

    /**
     * Test constructor with unconfigured container does not contain log
     *
     * @covers \App\Dependencies\ContainerBuilder::__construct
     *
     * @return void
     */
    public function testConstructUnconfiguredLog()
    {
        $this->expectException(UnknownIdentifierException::class);
        $this->assertNull($this->_c['log']);
    }

    /**
     * Test constructor with unconfigured container does not contain cors_config
     *
     * @covers \App\Dependencies\ContainerBuilder::__construct
     *
     * @return void
     */
    public function testConstructUnconfiguredCorsConfig()
    {
        $this->expectException(UnknownIdentifierException::class);
        $this->assertNull($this->_c['cors_config']);
    }

    /**
     * Test constructor has default error handler implementations
     *
     * @covers \App\Dependencies\ContainerBuilder::__construct
     *
     * @return void
     */
    public function testConstructDefaultErrorHandlers()
    {
        $this->assertInstanceOf(Error::class, $this->_c['errorHandler']);
        $this->assertInstanceOf(NotAllowed::class, $this->_c['notAllowedHandler']);
        $this->assertInstanceOf(NotFound::class, $this->_c['notFoundHandler']);
        $this->assertInstanceOf(PhpError::class, $this->_c['phpErrorHandler']);
    }

    /**
     * Test buildCORSDependencies method with injected configuration
     *
     * @uses   \App\Dependencies\ContainerBuilder::__construct
     * @covers \App\Dependencies\ContainerBuilder::buildCORSDependencies
     *
     * @return void
     */
    public function testBuildCORSDependencies()
    {
        $this->_c['ini'] = function () {
            return ['API' => ['CORS_URLs' => join(',', self::CORS_URLS)]];
        };

        $this->_c['log'] = $this->createMock(Logger::class);

        $this->_builder->buildCORSDependencies();

        $expected = [
            'origin' => self::CORS_URLS,
            'methods' => ContainerBuilder::CORS_METHODS,
            'headers.allow' => ContainerBuilder::CORS_HEADERS_ALLOW,
            'headers.expose' => ContainerBuilder::CORS_HEADERS_EXPOSE,
            'credentials' => ContainerBuilder::CORS_CREDENTIALS,
            'cache' => ContainerBuilder::CORS_CACHE,
            'logger' => $this->_c['log']
        ];

        $this->assertSame($expected, $this->_c['cors_config']);
    }

    /**
     * Test buildCORSDependencies method with .ini file configuration
     *
     * @uses   \App\Dependencies\ContainerBuilder::__construct
     * @uses   \App\Dependencies\ContainerBuilder::buildIniDependencies
     * @covers \App\Dependencies\ContainerBuilder::buildCORSDependencies
     *
     * @return void
     */
    public function testBuildCORSDependenciesWithIni()
    {
        $log = $this->createMock(Logger::class);
        $this->_c['log'] = $log;
        $this->_c['defaults'] = [
            'log_dir' => '../../test/logs',
            'ini_name' => $this->_root->getChild('ini/test.ini')->url()
        ];

        // construct ini settings from file
        $this->_builder->buildIniDependencies();

        $this->_builder->buildCORSDependencies();

        $expected = [
            'origin' => self::CORS_URLS,
            'methods' => ContainerBuilder::CORS_METHODS,
            'headers.allow' => ContainerBuilder::CORS_HEADERS_ALLOW,
            'headers.expose' => ContainerBuilder::CORS_HEADERS_EXPOSE,
            'credentials' => ContainerBuilder::CORS_CREDENTIALS,
            'cache' => ContainerBuilder::CORS_CACHE,
            'logger' => $log
        ];

        $this->assertSame($expected, $this->_c['cors_config']);
        $this->assertSame($log, $this->_c['cors_config']['logger']);
    }

    /**
     * Test getContainer method
     *
     * @uses   \App\Dependencies\ContainerBuilder::__construct
     * @covers \App\Dependencies\ContainerBuilder::getContainer
     *
     * @return void
     */
    public function testGetContainer()
    {
        $this->_c['foo'] = 'test';
        $this->assertNotNull($this->_builder);
        $this->assertInstanceOf(ContainerBuilder::class, $this->_builder);
        $this->assertSame($this->_c, $this->_builder->getContainer());
        $this->assertEquals('test', $this->_builder->getContainer()['foo']);
    }

    /**
     * Test ini file dependency builder
     *
     * @uses   \App\Dependencies\ContainerBuilder::__construct
     * @covers \App\Dependencies\ContainerBuilder::buildIniDependencies
     *
     * @return void
     */
    public function testBuildIniDependenciesFromFile()
    {
        $this->_c['defaults'] = [
            'ini_name' => $this->_root->getChild('ini/test.ini')->url()
        ];

        $this->_builder->buildIniDependencies();

        $expected = [
            'API' => [
                'API_URL' => 'http://localhost:8000/',
                'LogPath' => '/app/test/logs',
                'LogThreshold' => '100',
                'CORS_URLs' => join(',', self::CORS_URLS)
            ]
        ];

        $this->assertSame($expected, $this->_c['ini']);
    }

    /**
     * Test ini file dependency builder with a missing .ini file throws exception
     *
     * @uses   \App\Dependencies\ContainerBuilder::__construct
     * @covers \App\Dependencies\ContainerBuilder::buildIniDependencies
     *
     * @return void
     *
     * @throws MockEnabledException
     */
    public function testBuildIniDependenciesWithMissingFile()
    {
        // turn off the error log
        $this->_spyErrorLog->enable();

        $this->_c['defaults'] = ['ini_name' => 'doesnotexist.ini'];

        $this->expectException(\InvalidArgumentException::class);
        $this->_builder->buildIniDependencies();
        $this->_c['ini']; // should trigger exception
        $this->fail('expected exception');
    }

    /**
     * Test ini file dependency builder with missing .ini file writes error_log
     *
     * @uses   \App\Dependencies\ContainerBuilder::__construct
     * @covers \App\Dependencies\ContainerBuilder::buildIniDependencies
     *
     * @return void
     *
     * @throws MockEnabledException
     */
    public function testBuildIniDependenciesWithMissingFileWritesErrorLog()
    {
        $this->_spyErrorLog->enable();

        $this->_c['defaults'] = ['ini_name' => 'doesnotexist.ini'];

        $this->_builder->buildIniDependencies();

        try {
            $this->_c['ini']; // should trigger exception
        } catch (\InvalidArgumentException $e) {
            $this->assertEquals(
                ["Could not find configuration file: doesnotexist.ini"],
                $this->_spyErrorLog->getInvocations()[0]->getArguments()
            );
        }
    }

    /**
     * Test ini file dependency builder with a missing .ini file throws exception
     *
     * @uses   \App\Dependencies\ContainerBuilder::__construct
     * @covers \App\Dependencies\ContainerBuilder::buildIniDependencies
     *
     * @return void
     *
     * @throws MockEnabledException
     */
    public function testBuildIniDependenciesWithInvalidFile()
    {
        $this->_spyErrorLog->enable();

        $this->_c['defaults'] = [
            'ini_name' => $this->_root->getChild('ini/invalid.ini')->url()
        ];

        $this->assertTrue($this->_root->hasChild('ini/invalid.ini'));
        $this->expectException(\InvalidArgumentException::class);
        $this->_builder->buildIniDependencies();
        $this->_c['ini']; // should trigger exception
        $this->fail('expected exception');
    }

    /**
     * Test ini file dependency builder with a missing .ini file writes error_log
     *
     * @uses   \App\Dependencies\ContainerBuilder::__construct
     * @covers \App\Dependencies\ContainerBuilder::buildIniDependencies
     *
     * @return void
     *
     * @throws MockEnabledException
     */
    public function testBuildIniDependenciesWithInvalidFileWritesErrorLog()
    {
        $this->_spyErrorLog->enable();

        $invalidFile = $this->_root->getChild('ini/invalid.ini')->url();

        $this->_c['defaults'] = ['ini_name' => $invalidFile];

        $this->assertTrue($this->_root->hasChild('ini/invalid.ini'));

        $this->_builder->buildIniDependencies();

        try {
            $this->_c['ini']; // should trigger exception
        } catch (\InvalidArgumentException $e) {
            $this->assertEquals(
                ["Could not read configuration file: " . $invalidFile],
                $this->_spyErrorLog->getInvocations()[0]->getArguments()
            );
        }
    }

    /**
     * Test application dependency builder
     *
     * @uses   \App\Dependencies\ContainerBuilder::__construct
     * @covers \App\Dependencies\ContainerBuilder::buildApplicationDependencies
     *
     * @return void
     */
    public function testBuildApplicationDependencies()
    {
        $this->_c['ini'] = function () {
            return ['API' => ['API_URL' => 'http://localhost:8000/']];
        };

        $this->_builder->buildApplicationDependencies();

        $expected = ['url' => 'http://localhost:8000/'];

        $this->assertSame($expected, $this->_c['api']);
    }

    /**
     * Test application dependency builder from .ini file
     *
     * @uses   \App\Dependencies\ContainerBuilder::__construct
     * @uses   \App\Dependencies\ContainerBuilder::buildIniDependencies
     * @covers \App\Dependencies\ContainerBuilder::buildApplicationDependencies
     *
     * @return void
     */
    public function testBuildApplicationDependenciesWithIni()
    {
        $this->_c['defaults'] = [
            'log_dir' => '../../test/logs',
            'ini_name' => $this->_root->getChild('ini/test.ini')->url()
        ];

        // construct ini settings from file
        $this->_builder->buildIniDependencies();

        $this->_builder->buildApplicationDependencies();

        $expected = ['url' => 'http://localhost:8000/'];

        $this->assertSame($expected, $this->_c['api']);
    }

    /**
     * Test log dependency builder
     *
     * @uses   \App\Dependencies\ContainerBuilder::__construct
     * @covers \App\Dependencies\ContainerBuilder::buildLoggingDependencies
     *
     * @return void
     */
    public function testBuildLoggingDependencies()
    {
        $this->_c['ini'] = self::TEST_EMPTY_INI;
        $this->_c['defaults'] = self::TEST_SETTINGS;

        $this->_builder->buildLoggingDependencies();

        $this->assertInstanceOf(Logger::class, $this->_c->get('log'));
    }

    /**
     * Test log dependency builder with default path
     *
     * @uses   \App\Dependencies\ContainerBuilder::__construct
     * @covers \App\Dependencies\ContainerBuilder::buildLoggingDependencies
     *
     * @return void
     */
    public function testBuildLoggingDependenciesDefaultPath()
    {
        $this->_c['ini'] = self::TEST_EMPTY_INI;
        $this->_c['defaults'] = self::TEST_SETTINGS;

        $this->_builder->buildLoggingDependencies();

        $this->assertInstanceOf(Logger::class, $this->_c->get('log'));
        $this->assertEquals('./test/logs', $this->_c['log_config']['log_dir']);
    }

    /**
     * Test log dependency builder with path defined in .ini file
     *
     * @uses   \App\Dependencies\ContainerBuilder::__construct
     * @covers \App\Dependencies\ContainerBuilder::buildLoggingDependencies
     *
     * @return void
     */
    public function testBuildLoggingDependenciesWithIniFilePath()
    {
        $settings = self::TEST_SETTINGS;
        $settings['log_dir'] = 'X';
        $this->_c['defaults'] = $settings;

        $this->_c['ini'] = function () {
            return ['API' => ['LogPath' => './test/logs']];
        };

        $this->_builder->buildLoggingDependencies();

        $this->assertInstanceOf(Logger::class, $this->_c->get('log'));
        $this->assertEquals('./test/logs', $this->_c['log_config']['log_dir']);
    }

    /**
     * Test log dependency builder with default path
     *
     * @uses   \App\Dependencies\ContainerBuilder::__construct
     * @covers \App\Dependencies\ContainerBuilder::buildLoggingDependencies
     *
     * @return void
     */
    public function testBuildLoggingDependenciesExistingPath()
    {
        $this->_c['ini'] = self::TEST_EMPTY_INI;
        $this->_c['defaults'] = self::TEST_SETTINGS;

        $this->_builder->buildLoggingDependencies();

        $this->assertInstanceOf(Logger::class, $this->_c->get('log'));
        $this->assertEquals('./test/logs', $this->_c['log_config']['log_dir']);
    }

    /**
     * Test log dependency builder with default signal level
     *
     * @uses   \App\Dependencies\ContainerBuilder::__construct
     * @covers \App\Dependencies\ContainerBuilder::buildLoggingDependencies
     *
     * @return void
     */
    public function testBuildLoggingDependenciesDefaultSignalLevel()
    {
        $this->_c['ini'] = self::TEST_EMPTY_INI;
        $this->_c['defaults'] = self::TEST_SETTINGS;

        $this->_builder->buildLoggingDependencies();

        $this->assertInstanceOf(Logger::class, $this->_c->get('log'));
        $this->assertEquals(Logger::DEBUG, $this->_c['log_config']['log_signal']);
    }

    /**
     * Test log dependency builder with signal level defined in .ini file
     *
     * @uses   \App\Dependencies\ContainerBuilder::__construct
     * @covers \App\Dependencies\ContainerBuilder::buildLoggingDependencies
     *
     * @return void
     */
    public function testBuildLoggingDependenciesWithIniFileSignalLevel()
    {
        $this->_c['defaults'] = self::TEST_SETTINGS;
        $this->_c['ini'] = function () {
            return ['API' => [
                'LogPath' => './test/logs',
                'LogThreshold' => Logger::ALERT]];
        };

        $this->_builder->buildLoggingDependencies();

        $this->assertInstanceOf(Logger::class, $this->_c->get('log'));
        $this->assertEquals(Logger::ALERT, $this->_c['log_config']['log_signal']);
    }

    /**
     * Test log dependency builder with non-DEBUG settings enabled
     *
     * @uses   \App\Dependencies\ContainerBuilder::__construct
     * @covers \App\Dependencies\ContainerBuilder::buildLoggingDependencies
     *
     * @return void
     */
    public function testBuildLoggingDependenciesNonDEBUGSettings()
    {
        $this->_c['ini'] = self::TEST_EMPTY_INI;
        $settings = self::TEST_SETTINGS;
        $settings['log_signal'] = Logger::INFO;
        $this->_c['defaults'] = $settings;

        error_reporting(E_WARNING);
        ini_set('display_errors', 0);
        ini_set('display_startup_errors', 0);

        $this->_builder->buildLoggingDependencies();

        $this->assertInstanceOf(Logger::class, $this->_c->get('log'));
        $this->assertEquals(E_WARNING, error_reporting());
        $this->assertEquals(0, ini_get('display_errors'));
        $this->assertEquals(0, ini_get('display_startup_errors'));
    }

    /**
     * Test log dependency builder with DEBUG settings enabled
     *
     * @uses   \App\Dependencies\ContainerBuilder::__construct
     * @covers \App\Dependencies\ContainerBuilder::buildLoggingDependencies
     *
     * @return void
     */
    public function testBuildLoggingDependenciesDEBUGSettings()
    {
        $this->_c['ini'] = self::TEST_EMPTY_INI;
        $this->_c['defaults'] = self::TEST_SETTINGS;

        error_reporting(E_WARNING);
        ini_set('display_errors', 0);
        ini_set('display_startup_errors', 0);

        $this->_builder->buildLoggingDependencies();

        $this->assertInstanceOf(Logger::class, $this->_c->get('log'));
        $this->assertEquals(E_ALL, error_reporting());
        $this->assertEquals(1, ini_get('display_errors'));
        $this->assertEquals(1, ini_get('display_startup_errors'));
    }

    /**
     * Test createLogDir
     *
     * @uses   \App\Dependencies\ContainerBuilder::__construct
     * @covers \App\Dependencies\ContainerBuilder::createLogDir
     *
     * @return void
     */
    public function testCreateLogDir()
    {
        $this->assertFalse($this->_root->hasChild('logs'));

        // do not use the standard mock because it stubs out createLogDir
        $builder = new ContainerBuilder($this->_c);
        $this->assertTrue($builder->createLogDir($this->_root->url() . '/logs'));

        $this->assertTrue($this->_root->hasChild('logs'));
    }

    /**
     * Test createLogDir where directory already exists
     *
     * @uses   \App\Dependencies\ContainerBuilder::__construct
     * @covers \App\Dependencies\ContainerBuilder::createLogDir
     *
     * @return void
     */
    public function testCreateLogDirAlreadyExists()
    {
        $this->assertFalse($this->_root->hasChild('logs'));
        vfsStream::newDirectory('logs')->at($this->_root);
        $this->assertTrue($this->_root->hasChild('logs'));

        // do not use the standard mock because it stubs out createLogDir
        $builder = new ContainerBuilder($this->_c);
        $this->assertTrue($builder->createLogDir($this->_root->url() . '/logs'));

        $this->assertTrue($this->_root->hasChild('logs'));
    }

    /**
     * Test createLogDir where directory is not created
     *
     * @uses   \App\Dependencies\ContainerBuilder::__construct
     * @covers \App\Dependencies\ContainerBuilder::createLogDir
     *
     * @return void
     *
     * @throws MockEnabledException
     */
    public function testCreateLogDirFailsToCreateDir()
    {
        $this->_spyErrorLog->enable();

        // deny write access to the root folder
        $this->_root->chmod(0555);

        $this->assertFalse($this->_root->hasChild('logs'));

        $logDir = $this->_root->url() . '/logs';

        // do not use the standard mock because it stubs out createLogDir
        $builder = new ContainerBuilder($this->_c);
        $this->assertFalse($builder->createLogDir($logDir));

        $this->assertFalse($this->_root->hasChild('logs'));

        $this->assertEquals(
            ["Unable to create log directory: " . $logDir],
            $this->_spyErrorLog->getInvocations()[0]->getArguments()
        );
    }

    /**
     * Test error handler dependency builder
     *
     * @uses   \App\Handler\CustomErrorHandler::__construct
     * @uses   \App\Handler\CustomNotAllowedHandler::__construct
     * @uses   \App\Handler\CustomNotFoundHandler::__construct
     * @uses   \App\Handler\CustomPHPErrorHandler::__construct
     * @uses   \App\Dependencies\ContainerBuilder::__construct
     * @covers \App\Dependencies\ContainerBuilder::_registerCustomErrorHandler()
     * @covers \App\Dependencies\ContainerBuilder::_registerNotAllowedHandler()
     * @covers \App\Dependencies\ContainerBuilder::_registerNotFoundHandler()
     * @covers \App\Dependencies\ContainerBuilder::_registerPhpErrorHandler()
     * @covers \App\Dependencies\ContainerBuilder::buildErrorHandlerDependencies
     *
     * @return void
     */
    public function testBuildErrorHandlerDependencies()
    {
        $this->_builder->buildErrorHandlerDependencies();
        $c = $this->_c;

        $this->assertInstanceOf(CustomErrorHandler::class, $c['errorHandler']);
        $this->assertInstanceOf(CustomNotFoundHandler::class, $c['notFoundHandler']);
        $this->assertInstanceOf(
            CustomNotAllowedHandler::class,
            $c['notAllowedHandler']
        );
        $this->assertInstanceOf(CustomPHPErrorHandler::class, $c['phpErrorHandler']);
    }
}
