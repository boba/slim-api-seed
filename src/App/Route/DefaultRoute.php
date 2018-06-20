<?php
/**
 * PHP version 7
 *
 * / route strategy
 *
 * @category Route
 * @package  App\Route
 * @author   Bob Anderson <25436+boba@users.noreply.github.com>
 * @license  http://www.opensource.org/licenses/mit-license.html MIT License
 * @link     https://github.com/boba/slim-api-seed Slim API Seed
 */

namespace App\Route;

use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class DefaultRoute
 *
 * Default route strategy
 *
 * @category Route
 * @package  App\Route
 * @author   Bob Anderson <25436+boba@users.noreply.github.com>
 * @license  http://www.opensource.org/licenses/mit-license.html MIT License
 * @link     https://github.com/boba/slim-api-seed Slim API Seed
 * phpcs:disable
 * @SWG\Swagger(
 *     basePath="/imgapi",
 *     host="localhost",
 *     schemes={"http", "https"},
 *     produces={"application/json"},
 *     consumes={"application/json"},
 *     @SWG\Info(
 *         title="Slim Framework API Seed",
 *         version="0.1.0"
 *     ),
 *     @SWG\Definition(
 *         definition="errorModel",
 *         required={"code", "message"},
 *         @SWG\Property(
 *             property="result",
 *             type="boolean",
 *             default="false"
 *         ),
 *         @SWG\Property(
 *             property="error",
 *             type="boolean",
 *             default="true"
 *         ),
 *         @SWG\Property(
 *             property="message",
 *             type="string"
 *         )
 *     )
 * )
 * phpcs:enable
 */
class DefaultRoute
{
    /**
     * Route strategy
     *
     * @param Request  $request  Request object
     * @param Response $response Response object
     * @param array    $args     Arguments
     *
     * @return Response
     * phpcs:disable
     * @SWG\Get(
     *     path="/",
     *     tags={"documents"},
     *     deprecated=true,
     *     description="Default route.",
     *     operationId="info",
     *     produces={"text/html"},
     *     @SWG\Response(
     *         response=200,
     *         description="Successful Response",
     *         @SWG\Schema(
     *             type="string",
     *             format="byte"
     *         )
     *     ),
     *     @SWG\Response(
     *         response="default",
     *         description="unexpected error",
     *         @SWG\Schema(
     *             ref="#/definitions/errorModel"
     *         )
     *     )
     * )
     * phpcs:enable
     */
    public function __invoke(Request $request, Response $response, array $args)
    {
        return $response;
    }
}
