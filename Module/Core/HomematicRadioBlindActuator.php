<?php
/**
 * Homematic Radio Blind-Actuator Module.
 *
 * @package     Smarthome
 * @subpackage  Module
 * @author      Robert Gies <mail@rgies.com>
 * @copyright   Copyright © 2014 by Robert Gies
 * @license     New BSD License
 * @date        2014-01-10
 */

/**
 * Homematic Radio Blind-Actuator Class.
 */
class Module_Core_HomematicRadioBlindActuator extends Module_Abstract
{
    /**
     * Required parameters for valid configuration.
     *
     * @var array
     */
    protected $_requiredParams = array('class', 'label', 'device_id');

    /**
     * Gets html code shown in configured panel.
     *
     * @var array vars Variables from ccu response
     * @return string Html code
     */
    public function renderHtml($vars = array())
    {
        $html = '';

        $idOn = $this->_id . '-on';
        $idOff = $this->_id . '-off';
        $onCss = 'btn btn-default disabled';
        $offCss = 'btn btn-default disabled';
        $clickOn = "";
        $clickOff = "";

        $status = (isset($vars['var' . $this->_id])) ? $vars['var' . $this->_id] : null;

        if ($this->_config['device_id'] && !is_null($status))
        {
            $onCss = 'btn btn-default';
            $offCss = 'btn btn-default';

            if ($status > 0.9)
            {
                $onCss = 'btn btn-primary active';
            }
            elseif ($status < 0.1)
            {
                $offCss = 'btn btn-primary active';
            }

            // click on up
            $uri = $this->_getAjaxUrl('setStatus', array($this->_config['device_id'], 100.0));
            $clickOn = "\$('#" . $idOn . "').attr('class', 'btn btn-default active');";
            $clickOn .= "\$('#" . $idOff . "').attr('class', 'btn btn-default');";
            $clickOn .= "$.get( '" . $uri . "', function( data ) {});";

            // click on down
            $uri = $this->_getAjaxUrl('setStatus', array($this->_config['device_id'], 0.0));
            $clickOff = "\$('#" . $idOff . "').attr('class', 'btn btn-default active');";
            $clickOff .= "\$('#" . $idOn . "').attr('class', 'btn btn-default');";
            $clickOff .= "$.get( '" . $uri . "', function( data ) {});";
        }

        $html .= '<div style="height: 30px">';
        $html .= '<div style="float: left">';
        $html .= htmlentities($this->_config['label'], ENT_QUOTES, 'UTF-8');
        $html .= '</div>';

        $html .= '<div style="float: right">';
        $html .= '<button id="' . $idOn . '" type="button" class="' . $onCss . '" onclick="'
            . $clickOn . '"><span class="glyphicon glyphicon-arrow-up"></span> Auf</button>&nbsp;';
        $html .= '<button id="' . $idOff . '" type="button" class="' . $offCss . '" onclick="'
            . $clickOff . '"><span class="glyphicon glyphicon-arrow-down"></span> Zu</button>';
        $html .= '</div>';
        $html .= '</div>';

        return $html;
    }

    /**
     * Script to get the required state information.
     *
     * @return string Script
     */
    public function getRequestScript()
    {
        $script = '';

        if (isset($this->_config['device_id']) && $this->_config['device_id'])
        {
            $hm = new Lib_Core_Homematic();
            $script = $hm->getDeviceStatusScript($this->_id, $this->_config['device_id'], 'level');
        }

        return $script;
    }

    /**
     * Ajax action to set the device level to 0.0 or 100.0.
     *
     * @param array $params Array([device_id], [level])
     */
    public static function setStatusAjaxAction(array $params)
    {
        $hm = new Lib_Core_Homematic();
        $hm->setValue($params[0], 'LEVEL', $params[1]);
    }
} 