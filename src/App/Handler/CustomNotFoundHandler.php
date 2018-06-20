<?php
/**
 * PHP version 7
 *
 * CustomNotFoundHandler - Error Handler
 *
 * @category Error
 * @package  App\Handler
 * @author   Bob Anderson
 * @license  http://www.opensource.org/licenses/mit-license.html MIT License
 * @link     https://github.com/boba/slim-api-seed Slim API Seed
 */

namespace App\Handler;

use Psr\Container\ContainerInterface;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class CustomNotFoundHandler
 *
 * @category Error
 * @package  App\Handler
 * @author   Bob Anderson
 * @license  http://www.opensource.org/licenses/mit-license.html MIT License
 * @link     https://github.com/boba/slim-api-seed Slim API Seed
 */
class CustomNotFoundHandler
{
    private $_container;

    /**
     * CustomNotFoundHandler constructor.
     *
     * @param ContainerInterface $container DI container
     */
    public function __construct(ContainerInterface $container = null)
    {
        if ($container == null) {
            throw new \InvalidArgumentException("Invalid PSR Container");
        }

        $this->_container = $container;
    }

    /**
     * Log error and send a custom JSON response with the error details
     *
     * @param Request  $request  Request object
     * @param Response $response Response object
     *
     * @return Response
     */
    public function __invoke(Request $request, Response $response)
    {

        $message = 'Route not found for resource: ' . $request->getUri();
        $content = ['error' => true, 'message' => $message];

        $this->_container->get('log')->error('NotFound error: ' . $message);

        return $response
            ->withStatus(404) // HTTP 404 NOT FOUND
            ->withHeader('Content-Type', 'application/json')
            ->withJson($content);
    }
}
