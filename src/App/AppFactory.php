<?php
/**
 * PHP version 7
 *
 * App Factory
 *
 * @category Application
 * @package  App
 * @author   Bob Anderson
 * @license  http://www.opensource.org/licenses/mit-license.html MIT License
 * @link     https://github.com/boba/slim-api-seed Slim API Seed
 */

namespace App;

use App\Dependencies\DependencyDirector;
use App\Route\RouteFactory;
use Monolog\Logger;
use Psr\Container\ContainerInterface;
use Slim\Container;
use Slim\Http\Request;
use Slim\Http\Response;
use Tuupola\Middleware\CorsMiddleware;

define(
    'APP_SETTINGS',
    [
        'defaults' => [
            'logger' => '',
            'logger_name' => 'API_LOGGER',
            'log_dir' => '../../logs',
            'log_file' => 'api.log',
            'log_signal' => Logger::INFO, // debug,info,notice,warning,critical
            'app_path' => realpath(dirname(__DIR__)) . '/src',
            'ini_name' => '../../app.ini',
            'displayErrorDetails' => true
        ]
    ]
);

/**
 * Class App
 *
 * App Factory
 *
 * @category Application
 * @package  App
 * @author   Bob Anderson
 * @license  http://www.opensource.org/licenses/mit-license.html MIT License
 * @link     https://github.com/boba/slim-api-seed Slim API Seed
 */
class AppFactory
{
    private $_container;
    private $_director;
    private $_routeFactory;

    /**
     * AppFactory constructor.
     *
     * @param Container|null          $container    DI container
     * @param DependencyDirector|null $director     Dependency builder director
     * @param RouteFactory|null       $routeFactory Route factory
     */
    public function __construct($container = null, DependencyDirector $director = null, RouteFactory $routeFactory = null)
    {
        $this->_container = $container;
        $this->_director = $director;
        $this->_routeFactory = $routeFactory;
    }

    /**
     * DI container
     *
     * @param ContainerInterface $container DI container
     *
     * @return void
     */
    public function setContainer($container)
    {
        $this->_container = $container;
    }

    /**
     * Director for building dependencies
     *
     * @param DependencyDirector $director Director for building dependencies
     *
     * @return void
     */
    public function setDirector(DependencyDirector $director)
    {
        $this->_director = $director;
    }

    /**
     * Route Factory for building routes
     *
     * @param RouteFactory $routeFactory Factory for building routes
     *
     * @return void
     */
    public function setRouteFactory(RouteFactory $routeFactory)
    {
        $this->_routeFactory = $routeFactory;
    }

    /**
     * Factory method to create a DI container
     *
     * @return ContainerInterface
     */
    public function createContainer()
    {
        if ($this->_container == null) {
            $this->_container = new Container(APP_SETTINGS);
        }

        return $this->_container;
    }

    /**
     * Factory method to create a dependency director
     *
     * @return DependencyDirector
     */
    public function createDirector()
    {
        if ($this->_director == null) {
            $this->_director = new DependencyDirector($this->createContainer());
        }

        return $this->_director;
    }

    /**
     * Factory method to create a route factory
     *
     * @return RouteFactory
     */
    public function createRouteFactory()
    {
        if ($this->_routeFactory == null) {
            $this->_routeFactory = new RouteFactory($this->createContainer());
        }

        return $this->_routeFactory;
    }

    /**
     * Factory method to create a \Slim\App
     *
     * @return \Slim\App
     * @throws \Interop\Container\Exception\ContainerException
     */
    public function createApp()
    {
        $factory = $this;

        // Create the container
        $container = $factory->createContainer();

        // Create the Slim App
        $app = new \Slim\App($container);

        // Configure container
        $director = $factory->createDirector();
        $director->constructContainer();

        // Add CORS middleware
        $cors = $container->get('cors_config');
        $app->add(new CorsMiddleware($cors));
        $app->getContainer()->get('log')->debug('CORS Middleware added');

        // Configure routes
        $routeFactory = $factory->createRouteFactory();
        $routeFactory->createDefaultRoutes($app);
        $routeFactory->createHelloRoutes($app);
        $routeFactory->createSwaggerRoutes($app);

        return $app;
    }
}
