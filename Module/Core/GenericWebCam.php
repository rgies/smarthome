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
        $label = $config['label'];

        $refreshCamJs = "$('#gcamimage_" . $id . "').attr('src', '" . $url
            . "&' + new Date().getTime());";

        $html .= '<div>';
        $html .= htmlentities($label, ENT_QUOTES, 'UTF-8');
        $html .= '</div>';
        $html .= '<div style="max-width:640px;"><img id="gcamimage_' . $id
            . '" style="max-width:100%; height: auto;" src="' . $url . '" />';

        // control buttons
        if (isset($config['buttons']) && is_array($config['buttons']['button']))
        {
            $html .= '<div class="btn-group">';

            $z = 0;
            foreach ($config['buttons']['button'] as $button)
            {
                $request = "$.get( '" . $this->_getAjaxUrl('callCam', array(base64_encode($label), $z))
                    . "', function(data) {" . $refreshCamJs . "});";
                $html .= '<button type="button" class="btn btn-default" onclick="' . $request
                    . '">' . $button['label'] . '</button> ';
                $z++;
            }

            $html .= '</div>';
        }

        $html .= '</div>';

        // javascript to refresh cam images
        if (isset($config['refresh']) && is_numeric($config['refresh']))
        {
            $int = (int)$config['refresh'] * 1000;
            $int = ($int<3000) ? 3000 : $int;
            $html .= '<script>setTimeout ("refreshCamImg_' . $id . '();", ' . $int . '); function refreshCamImg_' . $id
                . '(){ ' . $refreshCamJs . ' setTimeout ("refreshCamImg_' . $id . '()", ' . $int . ');};</script>';
        }

        return $html;
    }

    /**
     * Ajax action to call a get request to the webcam.
     *
     * @param array $params Array([device_id], [status])
     */
    public static function callCamAjaxAction(array $params)
    {
        $label = (string)base64_decode($params[0]);
        $count = (integer)$params[1];

        $config = new Lib_Core_Config();
        $module = $config->getModuleByLabel($label);

        $url = (string)$module[0]->buttons->button[$count]->url;

        echo file_get_contents($url);
        sleep(1);
    }
}