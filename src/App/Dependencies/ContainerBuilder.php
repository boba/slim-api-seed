<?php
/**
 * PHP version 7
 *
 * Builder object to build dependency injection containers
 *
 * DependencyDirector and ContainerBuild implement the Builder design pattern
 *
 * @category Dependencies
 * @package  App\Dependencies
 * @author   Bob Anderson
 * @license  http://www.opensource.org/licenses/mit-license.html MIT License
 * @link     https://github.com/boba/slim-api-seed Slim API Seed
 */

namespace App\Dependencies;

use App\Handler\CustomErrorHandler;
use App\Handler\CustomNotAllowedHandler;
use App\Handler\CustomNotFoundHandler;
use App\Handler\CustomPHPErrorHandler;
use App\Log\SessionProcessor;
use Monolog\Logger;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Formatter\LineFormatter;
use Psr\Container\ContainerInterface;

/**
 * Class ContainerBuilder
 *
 * Take an existing container and construct the content as directed
 * by a DependencyDirector.
 *
 * Implements the Builder portion of the Builder design pattern
 *
 * @category Dependencies
 * @package  App\Dependencies
 * @author   Bob Anderson
 * @license  http://www.opensource.org/licenses/mit-license.html MIT License
 * @link     https://github.com/boba/slim-api-seed Slim API Seed
 */
class ContainerBuilder
{
    /**
     * Log format
     */
    const LOG_FORMAT
        = "[%datetime%][%session%][%remoteIP%] %channel%.%level_name%: "
        . "%message% %context% %extra%\n";

    /**
     * CORS configuration
     */
    const CORS_METHODS = array('GET', 'POST', 'PUT', 'DELETE', 'OPTIONS');
    const CORS_HEADERS_ALLOW = array();
    const CORS_HEADERS_EXPOSE = array(
        "Content-Type",
        "X-Requested-With",
        "X-authentication",
        "X-client");
    const CORS_CREDENTIALS = true;
    const CORS_CACHE = 0;

    protected $container;

    /**
     * ContainerBuilder constructor.
     *
     * @param ContainerInterface $container DI container
     */
    public function __construct(ContainerInterface $container = null)
    {
        if ($container == null) {
            throw new \InvalidArgumentException("Invalid PSR Container");
        }

        $this->container = $container;
    }

    /**
     * The Dependency Injection container
     *
     * @return ContainerInterface DI container
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * Read the specified .ini file and override various system settings
     *
     * @return void
     */
    public function buildIniDependencies()
    {
        $this->container['ini'] = function (ContainerInterface $c) {
            $settings = $c->get('defaults');
            $name = $settings['ini_name'];

            // read .ini file for additional settings
            $filename = $name;

            if (file_exists($filename)) {
                $ini = parse_ini_file($filename, true);

                if ($ini == false) {
                    $error = "Could not read configuration file: " . $name;
                    error_log($error);
                    throw new \InvalidArgumentException($error);
                }

                return $ini;
            } else {
                $error = "Could not find configuration file: " . $name;
                error_log($error);
                throw new \InvalidArgumentException($error);
            }
        };
    }

    /**
     * Create some application settings
     *
     * @return void
     */
    public function buildApplicationDependencies()
    {
        $this->container['api'] = function (ContainerInterface $c) {
            $ini = $c->get('ini');

            /**
             * Get the API URL from the ini file and extract the protocol
             *
             * [API]
             * API_URL=...
             */

            $api['url'] = $ini['API']['API_URL'];

            return $api;
        };
    }

    /**
     * Configure logging
     *
     * @return void
     */
    public function buildLoggingDependencies()
    {
        $this->container['log_config'] = function (ContainerInterface $c) {

            $ini = $c->get('ini');

            $config = [
                'log_dir' => $c['defaults']['log_dir'],
                'log_file' => $c['defaults']['log_file'],
                'log_signal' => $c['defaults']['log_signal'],
                'log_path' => $c['defaults']['log_dir']
                    . DIRECTORY_SEPARATOR
                    . $c['defaults']['log_file']
            ];

            /**
             * Get the API LogPath from the ini file if available
             *
             * [API]
             * LogPath=...
             */
            if (isset($ini['API']['LogPath'])) {
                $config['log_dir'] = $ini['API']['LogPath'];
            }

            $config['log_path']
                = $config['log_dir']
                . DIRECTORY_SEPARATOR
                . $config['log_file'];

            /**
             * Get the API LogThreshold from the ini file
             *
             * [API]
             * LogThreshold=...
             *
             * $settings['log_signal'] contains the default log level
             */
            $config['log_signal'] = $c['defaults']['log_signal'];
            if (isset($ini['API']['LogThreshold'])) {
                $config['log_signal'] = $ini['API']['LogThreshold'];
            }

            return $config;
        };

        $this->container['log'] = function (\Slim\Container $c) {
            $settings = $c->get('defaults');
            $log_config = $c->get('log_config');

            // make sure the log directory exists
            $this->createLogDir($log_config['log_dir']);

            // enable additional php logging on debug
            if ($log_config['log_signal'] == LOGGER::DEBUG) {
                error_reporting(E_ALL);
                ini_set('display_errors', 1);
                ini_set('display_startup_errors', 1);
            }

            $log = new Logger($settings['logger_name']);

            // use a custom line formatter
            $formatter = new LineFormatter(ContainerBuilder::LOG_FORMAT);

            // create rotating file log handler
            $handler = new RotatingFileHandler(
                $log_config['log_path'],
                0,
                $log_config['log_signal']
            );
            $handler->setFormatter($formatter);
            $log->pushHandler($handler);

            $log->pushProcessor(new SessionProcessor);

            return $log;
        };
    }

    /**
     * Create the logging directory if it doesn't already exist
     *
     * @param string $dir Log directory
     *
     * @return bool Returns true if it already exists or it was created
     */
    public function createLogDir($dir)
    {
        // make sure the log directory exists
        if (!file_exists($dir)) {
            if (!mkdir($dir, 0777, true)) {
                error_log('Unable to create log directory: ' . $dir);
                return false;
            } else {
                // created log directory
                return true;
            }
        } else {
            // log directory already exists
            return true;
        }
    }

    /**
     * Create customer error handlers
     *
     * @return void
     */
    public function buildErrorHandlerDependencies()
    {
        $this->_registerCustomErrorHandler();
        $this->_registerNotAllowedHandler();
        $this->_registerNotFoundHandler();
        $this->_registerPhpErrorHandler();
    }

    /**
     * Create a configuration for CORS
     *
     * @return void
     */
    public function buildCORSDependencies()
    {
        $this->container['cors_config'] = function (ContainerInterface $c) {
            $ini = $c->get('ini');

            // CORS config
            $corsURLS = explode(",", $ini['API']['CORS_URLs']);

            $corsOptions = array(
                "origin" => $corsURLS, // "*" for all,
                "methods" => ContainerBuilder::CORS_METHODS,
                "headers.allow" => ContainerBuilder::CORS_HEADERS_ALLOW,
                "headers.expose" => ContainerBuilder::CORS_HEADERS_EXPOSE,
                "credentials" => ContainerBuilder::CORS_CREDENTIALS,
                "cache" => ContainerBuilder::CORS_CACHE,
                "logger" => $this->container['log']
            );

            return $corsOptions;
        };
    }

    /**
     * Add CustomPHPErrorHandler to the DI container as errorHandler
     *
     * @return void
     */
    private function _registerPhpErrorHandler()
    {
        $this->container['phpErrorHandler'] = function ($container) {
            return new CustomPHPErrorHandler($container);
        };
    }

    /**
     * Add CustomErrorHandler to the DI container as errorHandler
     *
     * @return void
     */
    private function _registerCustomErrorHandler()
    {
        $this->container['errorHandler'] = function ($container) {
            return new CustomErrorHandler($container);
        };
    }

    /**
     * Add CustomNotAllowedHandler to the DI container as notAllowedHandler
     *
     * @return void
     */
    private function _registerNotAllowedHandler()
    {
        $this->container['notAllowedHandler'] = function ($container) {
            return new CustomNotAllowedHandler($container);
        };
    }

    /**
     * Add CustomNotFoundHandler to the DI container as notFoundHandler
     *
     * @return void
     */
    private function _registerNotFoundHandler()
    {
        $this->container['notFoundHandler'] = function ($container) {
            return new CustomNotFoundHandler($container);
        };
    }
}
