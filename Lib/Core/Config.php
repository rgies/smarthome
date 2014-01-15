<?php
/**
 * Smarthome Configuration.
 *
 * @package     Smarthome
 * @subpackage  Lib_Smarthome
 * @author      Robert Gies <mail@rgies.com>
 * @copyright   Copyright © 2014 by Robert Gies
 * @license     New BSD License
 * @date        2014-01-10
 */

/**
 * Class Lib_Core_Config.
 */
class Lib_Core_Config
{
    protected static $_config;

    public function __construct()
    {
        $filename = SH_ROOT_PATH . 'Config/Config.xml';

        if (!file_exists($filename))
        {
            die('Bitte die Config.xml Datei anlegen !!!');
        }

        $this->_readConfig($filename);
    }

    protected function _readConfig($filename)
    {
        if (is_null(self::$_config))
        {
            $config = @simplexml_load_file($filename);

            if (! $config)
            {
                throw new Exception(error_get_last());
            }

            self::$_config = $config;
        }
    }

    public function getPanel($rowNr, $colNr)
    {
        return self::$_config->grid->row[$rowNr]->panel[$colNr];
    }

    public function getHost()
    {
        return self::$_config->app->host;
    }

    public function getGrid()
    {
        return self::$_config->grid;
    }

    public function getAlerts()
    {
        if (isset(self::$_config->alert))
        {
            return self::$_config->alert;
        }
        return null;
    }
}