<?php
/**
 * PHP version 7
 *
 * Bootstrap
 *
 * @category Application
 * @package  App
 * @author   Bob Anderson <25436+boba@users.noreply.github.com>
 * @license  http://www.opensource.org/licenses/mit-license.html MIT License
 * @link     https://github.com/boba/slim-api-seed Slim API Seed
 */

use App\App;

date_default_timezone_set('America/Detroit');

/**
 * Build an instance of the application
 */
try {
    $factory = new \App\AppFactory();
    $app = $factory->createApp();

    $container = $app->getContainer();
    $log = $container->get('log');

    $log->debug('Application bootstrap complete');
} catch (Exception $e) {
    // something has gone wrong with the application startup
    $error = [
        'error' => true,
        'message' => "Unable to start application services: " . $e->getMessage()
    ];

    // log error
    error_log($error['message']);

    // return HTTP error response
    header_remove();
    http_response_code(500);
    header('Content-type: application/json', true, 500);
    echo json_encode($error);

    exit;
}
