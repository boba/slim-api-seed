<?php
/**
 * PHP version 7
 *
 * CustomErrorHandler - Error Handler
 *
 * @category Error
 * @package  App\Handler
 * @author   Bob Anderson <25436+boba@users.noreply.github.com>
 * @license  http://www.opensource.org/licenses/mit-license.html MIT License
 * @link     https://github.com/boba/slim-api-seed Slim API Seed
 */

namespace App\Handler;

use Psr\Container\ContainerInterface;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class CustomErrorHandler
 *
 * @category Error
 * @package  App\Handler
 * @author   Bob Anderson <25436+boba@users.noreply.github.com>
 * @license  http://www.opensource.org/licenses/mit-license.html MIT License
 * @link     https://github.com/boba/slim-api-seed Slim API Seed
 */
class CustomErrorHandler
{
    private $_container;

    /**
     * CustomErrorHandler constructor.
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
     * @param Request    $request  Request object
     * @param Response   $response Response object
     * @param \Exception $exp      Exception
     *
     * @return Response
     */
    public function __invoke(Request $request, Response $response, \Exception $exp)
    {
        $message = 'Error: ' . $exp->getMessage();
        $content = ['error' => true, 'message' => $message];

        $this->_container->get('log')->error('ErrorHandler: ' . $message);

        return $response
            ->withStatus(500)
            ->withHeader('Content-Type', 'application/json')
            ->withJson($content);
    }
}
