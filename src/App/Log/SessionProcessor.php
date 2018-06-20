<?php
/**
 * PHP version 7
 *
 * SessionProcessor - Add session details to log records
 *
 * @category Logging
 * @package  App\Log
 * @author   Bob Anderson <25436+boba@users.noreply.github.com>
 * @license  http://www.opensource.org/licenses/mit-license.html MIT License
 * @link     https://github.com/boba/slim-api-seed Slim API Seed
 */

namespace App\Log;

/**
 * SessionProcessor
 *
 * @category Logging
 * @package  App\Log
 * @author   Bob Anderson <25436+boba@users.noreply.github.com>
 * @license  http://www.opensource.org/licenses/mit-license.html MIT License
 * @link     https://github.com/boba/slim-api-seed Slim API Seed
 */
class SessionProcessor
{
    /**
     * Add session details to log records
     *
     * @param array $record Log record
     *
     * @return array
     */
    public function __invoke(array $record)
    {
        //add session id and remote IP to the log record
        $record['session'] = '';
        if (session_id() != '') {
            $record['session'] = session_id();
        }

        $record['remoteIP'] = '';
        if (isset($_SERVER['REMOTE_ADDR'])) {
            $record['remoteIP'] = $_SERVER['REMOTE_ADDR'];
        }

        return $record;
    }
}
