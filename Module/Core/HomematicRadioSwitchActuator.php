<?php
/**
 * Homematic Radio Switch-Actuator Module.
 *
 * @package     Smarthome
 * @subpackage  Module
 * @author      Robert Gies <mail@rgies.com>
 * @copyright   Copyright © 2014 by Robert Gies
 * @license     New BSD License
 * @date        2014-01-10
 */

/**
 * Homematic Radio Switch-Actuator Class.
 */
class Module_Core_HomematicRadioSwitchActuator extends Module_Abstract
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
                if ($status == 'true')
                {
                    $onCss = 'btn btn-primary active';
                    $offCss = 'btn btn-default';
                }
                else
                {
                    $onCss = 'btn btn-default';
                    $offCss = 'btn btn-primary active';
                }

                $uri = $this->_getAjaxUrl('setStatus', array($this->_config['device_id'], 1));
                $clickOn = "\$('#" . $idOn . "').attr('class', 'btn btn-primary active');";
                $clickOn .= "\$('#" . $idOff . "').attr('class', 'btn btn-default');";
                $clickOn .= "$.get( '" . $uri . "', function( data ) {});";

                $uri = $this->_getAjaxUrl('setStatus', array($this->_config['device_id'], 0));
                $clickOff = "\$('#" . $idOff . "').attr('class', 'btn btn-primary active');";
                $clickOff .= "\$('#" . $idOn . "').attr('class', 'btn btn-default');";
                $clickOff .= "$.get( '" . $uri . "', function( data ) {});";
            }
        }

        $html .= '<div style="height: 30px">';
        $html .= '<span>';
        $html .= htmlentities($this->_config['label'], ENT_QUOTES, 'UTF-8');
        $html .= '</span>';

        $html .= '<span style="float: right">';
        $html .= '<button id="' . $idOn . '" type="button" class="' . $onCss . '" onclick="'
            . $clickOn . '">An</button>&nbsp;';
        $html .= '<button id="' . $idOff . '" type="button" class="' . $offCss . '" onclick="'
            . $clickOff . '">Aus</button>';
        $html .= '</span>';
        $html .= '</div>';

        return $html;
    }

//    /**
//     * Gets the current status of light device.
//     *
//     * @return bool True for on and false for off
//     */
//    protected function getStatus()
//    {
//        $status = null;
//
//        if (isset($this->_config['device_id']) && $this->_config['device_id'])
//        {
//            $hm = new Lib_Smarthome_Homematic();
//            $status = $hm->getState('BidCos-RF.' . $this->_config['device_id'] . '.STATE');
//            //$status = $hm->getValue($this->_config['device_id'], 'STATE');
//        }
//
//        return $status;
//    }

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
            $script = $hm->getDeviceStatusScript($this->_id, $this->_config['device_id']);
        }

        return $script;
    }

    /**
     * Ajax action to set the device status to on or off.
     *
     * @param array $params Array([device_id], [status])
     */
    public static function setStatusAjaxAction(array $params)
    {
        $value = ($params[1]) ? true : false;

        $hm = new Lib_Smarthome_Homematic();
        $hm->setValue($params[0], 'STATE', $value);
    }
} 