<?php
/**
 * PHP version 7
 *
 * CustomPHPErrorHandler - Error Handler
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
 * Class CustomPHPErrorHandler
 *
 * @category Error
 * @package  App\Handler
 * @author   Bob Anderson
 * @license  http://www.opensource.org/licenses/mit-license.html MIT License
 * @link     https://github.com/boba/slim-api-seed Slim API Seed
 */
class CustomPHPErrorHandler
{
    private $_container;

    /**
     * CustomPHPErrorHandler constructor.
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
     * @param string   $error    Error Message
     *
     * @return Response
     */
    public function __invoke(Request $request, Response $response, $error)
    {
        $message = 'PHP error';
        $content = ['error' => true, 'message' => $message];

        $this->_container->get('log')->error($message . ': ' . $error);

        ob_start();
        var_dump($response);
        $d = ob_get_clean();

        $this->_container->get('log')->error($response);
        $this->_container->get('log')->error($d);

        return $response
            ->withStatus(500) // HTTP 500 SYSTEM ERROR
            ->withHeader('Content-Type', 'application/json')
            ->withJson($content);
    }
}
