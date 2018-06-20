<?php
/**
 * PHP version 7
 *
 * CustomNotAllowedHandler - Error Handler
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
 * Class CustomNotAllowedHandler
 *
 * @category Error
 * @package  App\Handler
 * @author   Bob Anderson
 * @license  http://www.opensource.org/licenses/mit-license.html MIT License
 * @link     https://github.com/boba/slim-api-seed Slim API Seed
 */
class CustomNotAllowedHandler
{
    private $_container;

    /**
     * CustomNotAllowedHandler constructor.
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
     * @param array    $methods  Allowed methods
     *
     * @return Response
     */
    public function __invoke(Request $request, Response $response, array $methods)
    {
        $message = 'Method must be one of: ' . implode(', ', $methods);
        $content = ['error' => true, 'message' => $message];

        $this->_container->get('log')->error('NotAllowed Error: ' . $message);

        return $response
            ->withStatus(405) // HTTP 405 METHOD NOT ALLOWED
            ->withHeader('Allow', implode(', ', $methods))
            ->withHeader('Content-Type', 'application/json')
            ->withJson($content);
    }
}
