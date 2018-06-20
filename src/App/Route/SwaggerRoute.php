<?php
/**
 * PHP version 7
 *
 * /swagger route strategy
 *
 * @category Route
 * @package  App\Route
 * @author   Bob Anderson
 * @license  http://www.opensource.org/licenses/mit-license.html MIT License
 * @link     https://github.com/boba/slim-api-seed Slim API Seed
 */

namespace App\Route;

use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class SwaggerRoute
 *
 * /swagger route strategy
 *
 * @category Route
 * @package  App\Route
 * @author   Bob Anderson
 * @license  http://www.opensource.org/licenses/mit-license.html MIT License
 * @link     https://github.com/boba/slim-api-seed Slim API Seed
 */
class SwaggerRoute
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
     *     path="/swagger/swagger.json",
     *     tags={"documents"},
     *     description="Swagger API documentation.",
     *     operationId="swagger",
     *     produces={"application/json"},
     *     @SWG\Response(
     *         response=200,
     *         description="Successful Response",
     *         @SWG\Schema(
     *             @SWG\Property(
     *                 property="swagger",
     *                 type="string",
     *                 default="2.0",
     *                 description="Version"
     *             ),
     *             @SWG\Property(
     *                 property="info",
     *                 type="string",
     *                 default="2.0",
     *                 description="Version"
     *             ),
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
        $swagger = \Swagger\scan([__DIR__]);
        $response = $response->withJson($swagger);
        return $response;
    }
}
