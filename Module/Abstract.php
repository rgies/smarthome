<?php
/**
 * Smarthome Module Abstract.
 *
 * @package     Smarthome
 * @subpackage  Module
 * @author      Robert Gies <mail@rgies.com>
 * @copyright   Copyright (c) 2014 by Robert Gies
 * @license     New BSD License
 * @date        2014-01-10
 */

/**
 * Abstract class for Smarthome modules.
 */
abstract class Module_Abstract implements Module_Interface
{
    /**
     * Unique id for this module to use at html tags.
     *
     * @var string
     */
    protected $_id;

    /**
     * Configuration part from Config.xml for this module.
     *
     * @var array
     */
    protected $_config;

    /**
     * Needed fields for valid configuration.
     *
     * @var array
     */
    protected $_requiredFields = array('label');

    /**
     * Module constructor.
     *
     * @param array $config     Configuration as array for this module
     */
    final public function __construct($config)
    {
        $this->_id = uniqid();
        $this->_config = $config;
    }

    /**
     * Empty initialization called at end of constructor.
     */
    protected function _init()
    {
        // init function to overwrite at modules
    }

    /**
     * Script to get the required state information.
     *
     * @return string Script
     */
    public function getRequestScript()
    {
        return '';
    }

    /**
     * Gets url to call own ajax methods.
     *
     * @param string $methodName    Name of target ajax action
     * @param array $params         Array of params
     * @return string Url
     */
    protected function _getAjaxUrl($methodName, array $params)
    {
        $uri = 'ajax_request.php?module=' . get_called_class() . '&action=' . $methodName;

        foreach ($params as $key=>$param)
        {
            $uri .= '&params[' . $key . ']=' . urlencode($param);
        }

        return $uri;
    }
}
