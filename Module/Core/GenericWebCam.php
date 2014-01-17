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
        $id = $this->_id;
        $config = $this->_config;
        $html = '';

        $url = $config['url'];

        $html .= '<div>';
        $html .= htmlentities($config['label'], ENT_QUOTES, 'UTF-8');
        $html .= '</div>';
        $html .= '<div style="max-width:640px;"><img id="gcamimage_' . $id
            . '" style="max-width:100%; height: auto;" src="' . $url . '" /></div>';

        // javascript to refresh cam images
        if (isset($config['refresh']) && is_numeric($config['refresh']))
        {
            $int = (int)$config['refresh'] * 1000;
            $html .= '<script>setTimeout ("refreshCamImg_' . $id . '()", ' . $int . '); function refreshCamImg_' . $id
                . '(){ $("#gcamimage_' . $id . '").attr("src", "' . $url
                . '&" + new Date().getTime()); setTimeout ("refreshCamImg_' . $id . '()", ' . $int . ');};</script>';
        }

        return $html;
    }
}