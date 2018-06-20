<?php
/**
 * PHP version 7
 *
 * /hello route strategy
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
 * Class HelloRoute
 *
 * /hello route strategy
 *
 * @category Route
 * @package  App\Route
 * @author   Bob Anderson <25436+boba@users.noreply.github.com>
 * @license  http://www.opensource.org/licenses/mit-license.html MIT License
 * @link     https://github.com/boba/slim-api-seed Slim API Seed
 */
class HelloRoute
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
        $args = $request->getParsedBody();

        $format = strtolower($request->getParsedBodyParam('format', 'json'));

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
