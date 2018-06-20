<?php
/**
 * PHP version 7
 *
 * Static route strategy
 *
 * @category Route
 * @package  App\Route
 * @author   Bob Anderson <25436+boba@users.noreply.github.com>
 * @license  http://www.opensource.org/licenses/mit-license.html MIT License
 * @link     https://github.com/boba/slim-api-seed Slim API Seed
 */

namespace App\Route;

use Slim\Container;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class StaticRoute
 *
 * Static route strategy
 *
 * @category Route
 * @package  App\Route
 * @author   Bob Anderson <25436+boba@users.noreply.github.com>
 * @license  http://www.opensource.org/licenses/mit-license.html MIT License
 * @link     https://github.com/boba/slim-api-seed Slim API Seed
 */
class StaticRoute
{
    private $_container;
    private $_template;
    private $_view;

    /**
     * StaticRoute constructor.
     *
     * @param Container $container DI container
     * @param string    $template  Static route template
     *
     * @return void
     */
    public function __construct(Container $container, string $template)
    {
        $this->_container = $container;
        $this->_template = $template;
        $this->_view = $this->_container['view'];
    }

    /**
     * /home route strategy
     *
     * @param Request  $request  Request object
     * @param Response $response Response object
     * @param array    $args     Arguments
     *
     * @return Response
     * phpcs:disable
     * @SWG\Get(
     *     path="/home",
     *     tags={"static"},
     *     deprecated=false,
     *     description="Home route.",
     *     operationId="home",
     *     produces={"text/html"},
     *     @SWG\Response(
     *         response=200,
     *         description="Successful Response",
     *         @SWG\Schema(
     *             type="string",
     *             format="byte"
     *         )
     *     )
     * )
     * phpcs:enable
     */
    public function __invoke(Request $request, Response $response, array $args)
    {
        $template = $this->_template . '.html';
        $args['name'] = isset($args['name']) ? $args['name'] : 'No name given';

        return $this->_view->render(
            $response,
            $template,
            [
                'name' => $args['name'],
                'cache_status' => $this->_container['view_cache_status']
            ]
        );
    }
}
