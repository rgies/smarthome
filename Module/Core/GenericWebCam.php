<?php
/**
 * Homematic Generic WebCam.
 *
 * @package     Smarthome
 * @subpackage  Module
 * @author      Robert Gies <mail@rgies.com>
 * @copyright   Copyright © 2014 by Robert Gies
 * @license     New BSD License
 * @date        2014-01-10
 */

/**
 * Homematic Generic WebCam Class.
 */
class Module_Core_GenericWebCam extends Module_Abstract
{
    /**
     * Required parameters for valid configuration.
     *
     * @var array
     */
    protected $_requiredParams = array('class', 'label', 'url');

    /**
     * Gets html code shown in configured panel.
     *
     * @var array vars Variables from ccu response
     * @return string Html code
     */
    public function renderHtml($vars = array())
    {
        $html = '';

        $url = $this->_config['url'];

        $html .= '<div>';
        $html .= htmlentities($this->_config['label'], ENT_QUOTES, 'UTF-8');
        $html .= '</div>';
        $html .= '<img width="320px" height="240px" src="' . $url . '" />';

        return $html;
    }

}