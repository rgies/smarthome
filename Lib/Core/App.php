<?php
/**
 * Smarthome Application.
 *
 * @package     Smarthome
 * @subpackage  Lib_Smarthome
 * @author      Robert Gies <mail@rgies.com>
 * @copyright   Copyright © 2014 by Robert Gies
 * @license     New BSD License
 * @date        2014-01-10
 */

/**
 * Class Lib_Core_App.
 */
class Lib_Core_App
{
    public static $rootPath;

    public static $viewContent;

    /**
     * Init Application.
     */
    public function __construct($rootPath)
    {
        self::$rootPath = rtrim($rootPath, '/');

        // include init
        require_once (self::$rootPath . '/Config/Init.php');

        // Register Autoload
        spl_autoload_register(array($this, '_autoload'));
    }

    /**
     * Autoload function.
     *
     * @param string $className Class name to load
     * @return boolean
     */
    protected function _autoload( $className )
    {
        // Build directory name from class name
        $filename = self::$rootPath . '/' . str_replace('_', '/', $className) . '.php';

        if (!file_exists($filename) || !$retVal = include $filename)
        {
            return false;
        }

        return true;
    }

    public function render($layout='Default')
    {
        $contents = '';
        $view = 'Index';

        $vars = array(
            'contents' => $contents,
        );
        extract($vars);

        ob_start();
        require self::$rootPath . '/View/Index/' . $view . '.php';
        $contents = ob_get_clean();

        if ($layout)
        {
            ob_start();
            require self::$rootPath . '/View/' . $layout . 'Layout.php';
            $contents = ob_get_clean();
        }

        return $contents;
    }
}