<?php
/**
 * PHP version 7
 *
 * /hello/name route strategy
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
 * Class HelloNameRoute
 *
 * /hello/name route strategy
 *
 * @category Route
 * @package  App\Route
 * @author   Bob Anderson
 * @license  http://www.opensource.org/licenses/mit-license.html MIT License
 * @link     https://github.com/boba/slim-api-seed Slim API Seed
 */
class HelloNameRoute
{
    /**
     * Route strategy
     *
     * @param Request  $request  Request object
     * @param Response $response Response object
     * @param array    $args     Arguments
     *
     * @return Response
     */
    public function __invoke(Request $request, Response $response, array $args)
    {
        $format = strtolower($request->getQueryParam('format', 'json'));

        if ($format === 'html') {
            $name = $args['name'];
            $response->getBody()->write("<p id=\"hello\">Hello, $name.</p>");
            return $response;
        } else {
            $name = $args['name'];
            $content = ['error' => false, 'data' => ['hello' => $name]];
            $response = $response->withJson($content);
            return $response;
        }
    }
}
