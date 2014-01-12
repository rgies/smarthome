<?php
// Define application root
const SH_ROOT_PATH = '../';

// Init log file
ini_set('error_log', SH_ROOT_PATH . 'Log/' . date('Y-m-d-') . 'log.txt');

// Register Autoload
spl_autoload_register('_autoload');

/**
 * Autoload function.
 *
 * @param string $className Class name to load
 * @return boolean
 */
function _autoload( $className )
{
    // Build directory name from class name
    $filename = SH_ROOT_PATH . str_replace('_', '/', $className) . '.php';

    if (!file_exists($filename) || !$retVal = include $filename)
    {
        return false;
    }

    return true;
}
