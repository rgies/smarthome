<?php
/**
 * Homematic Radio Blind-Actuator Module.
 *
 * @package     Smarthome
 * @subpackage  Module
 * @author      Robert Gies <mail@rgies.com>
 * @copyright   Copyright (c) 2014 by Robert Gies
 * @license     New BSD License
 * @date        2014-01-10
 */

/**
 * Homematic Radio Blind-Actuator Class.
 */
class Module_Core_HomematicRadioBlindActuator extends Module_Abstract
{
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

        if (isset($this->_config['device_id']) && $this->_config['device_id'])
        {
            $status = (isset($vars['var' . $this->_id])) ? $vars['var' . $this->_id] : null;

            if (!is_null($status))
            {
                if ($status > 0.0)
                {
                    $onCss = 'btn btn-primary active';
                    $offCss = 'btn btn-default';
                }
                else
                {
                    $onCss = 'btn btn-default';
                    $offCss = 'btn btn-primary active';
                }

                $uri = $this->_getAjaxUrl('setStatus', array($this->_config['device_id'], 100.0));
                $clickOn = "\$('#" . $idOn . "').attr('class', 'btn btn-primary active');";
                $clickOn .= "\$('#" . $idOff . "').attr('class', 'btn btn-default');";
                $clickOn .= "$.get( '" . $uri . "', function( data ) {});";

                $uri = $this->_getAjaxUrl('setStatus', array($this->_config['device_id'], 0.0));
                $clickOff = "\$('#" . $idOff . "').attr('class', 'btn btn-primary active');";
                $clickOff .= "\$('#" . $idOn . "').attr('class', 'btn btn-default');";
                $clickOff .= "$.get( '" . $uri . "', function( data ) {});";
            }
        }

        $html .= '<div style="height: 30px">';
        $html .= '<div style="float: left">';
        $html .= htmlentities($this->_config['label']);
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
            $hm = new Lib_Smarthome_Homematic();
            $script = $hm->getDeviceStatusScript($this->_id, $this->_config['device_id'], 'level');
        }

        return $script;
    }

//    /**
//     * Gets the current status of light device.
//     *
//     * @return float True for on and false for off
//     */
//    protected function getStatus()
//    {
//        $status = null;
//
//        if (isset($this->_config['device_id']) && $this->_config['device_id'])
//        {
//            $hm = new Lib_Smarthome_Homematic();
//            //$status = $hm->getValue($this->_config['device_id'], 'LEVEL');
//            $status = $hm->getState('BidCos-RF.' . $this->_config['device_id'] . '.LEVEL');
//        }
//
//        return $status;
//    }

    /**
     * Ajax action to set the device level to 0.0 or 100.0.
     *
     * @param array $params Array([device_id], [level])
     */
    public static function setStatusAjaxAction(array $params)
    {
        $hm = new Lib_Smarthome_Homematic();
        $hm->setValue($params[0], 'LEVEL', $params[1]);
    }
} 