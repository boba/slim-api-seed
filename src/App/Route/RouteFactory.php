<?php
/**
 * PHP version 7
 *
 * File description
 *
 * @category Application
 * @package  App\Route
 * @author   Bob Anderson <25436+boba@users.noreply.github.com>
 * @license  http://www.opensource.org/licenses/mit-license.html MIT License
 * @link     https://github.com/boba/slim-api-seed Slim API Seed
 */

namespace App\Route;

use Psr\Container\ContainerInterface;
use Slim\App;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class RouteFactory
 *
 * Route Factory
 *
 * @category Route
 * @package  App\Route
 * @author   Bob Anderson <25436+boba@users.noreply.github.com>
 * @license  http://www.opensource.org/licenses/mit-license.html MIT License
 * @link     https://github.com/boba/slim-api-seed Slim API Seed
 */
class RouteFactory
{
    private $_container;

    /**
     * RouteFactory constructor.
     *
     * @param ContainerInterface|null $container DI container
     */
    public function __construct(ContainerInterface $container = null)
    {
        $this->_container = $container;
    }

    /**
     * Create / routes
     *
     * @param App $app The application
     *
     * @return void
     */
    public function createDefaultRoutes($app)
    {
        $app->get('/', new DefaultRoute());
    }

    /**
     * Create /hello routes
     *
     * @param App $app The application
     *
     * @return void
     */
    public function createHelloRoutes($app)
    {
        $app->get('/hello/{name}', new HelloNameRoute());
        $app->post('/hello', new HelloRoute());
    }

    /**
     * Create /swagger routes
     *
     * @param App $app The application
     *
     * @return void
     */
    public function createSwaggerRoutes($app)
    {
        $app->get('/swagger/swagger.json', new SwaggerRoute());
    }
}
