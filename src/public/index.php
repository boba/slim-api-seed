<?php
/**
 * PHP version 7
 *
 * Main application
 *
 * @category Application
 * @package  App
 * @author   Bob Anderson <25436+boba@users.noreply.github.com>
 * @license  http://www.opensource.org/licenses/mit-license.html MIT License
 * @link     https://github.com/boba/slim-api-seed Slim API Seed
 */

require_once __DIR__ . '/../../vendor/autoload.php';

/**
 * Bootstrap the Slim app
 *
 * @var \Slim\App $app
 */
require_once __DIR__ . '/../App/Bootstrap/bootstrap.php';
assert(isset($app) && $app instanceof \Slim\App); // did bootstrapping work?

/**
 * Start the app
 */
$app->run();
