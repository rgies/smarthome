<?php
/**
 * Homematic Wireless Room Thermostat [HM-TC-IT-WM-W-EU] Module.
 *
 * @package     Smarthome
 * @subpackage  Module
 * @author      Robert Gies <mail@rgies.com>
 * @copyright   Copyright © 2014 by Robert Gies
 * @license     New BSD License
 * @date        2014-01-10
 */

/**
 * Homematic Wireless Room Thermostat Class.
 */
class Module_Core_HomematicWirelessThermostat extends Module_Abstract
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
        $upScript   = '';
        $downScript = '';
        $buttonStyle = '';
        $modeTxt = 'Auto';

        // current temperature
        $temp1 = (isset($vars['vartemp1' . $this->_id])) ? $vars['vartemp1' . $this->_id] : '';

        // defined temperature
        $temp2 = (isset($vars['vartemp2' . $this->_id])) ? $vars['vartemp2' . $this->_id] : '';

        // valve state
        $valv = (isset($vars['varvalv' . $this->_id])) ? $vars['varvalv' . $this->_id] : '';

        // control mode
        $mode = (isset($vars['varmode' . $this->_id])) ? $vars['varmode' . $this->_id] : '0';

        // arrow icon
        $icon = ((float)$temp1 > (float)$temp2) ? 'arrow-down' : 'arrow-up';


        if ($temp1 == '')
        {
            $buttonStyle = ' disabled';
        }
        else
        {
            $tempId = '\'#setTemp_' . $this->_id . '\'';
            $modeId = '\'#btnCtrlMode_' . $this->_id . '\'';
            $uri    = $this->_getAjaxUrl('setTemperature', array($this->_config['device_id'], ''));
            $upScript   = 'val=parseFloat($(' . $tempId . ').html())+0.5;if (val>30) val=30;$(' . $modeId
                . ').html(\'Manuell\');$(' . $tempId . ').html(val.toFixed(1)); $.get( \'' . $uri
                . '\' + val, function( data ) {});';
            $downScript = 'val=parseFloat($(' . $tempId . ').html())-0.5;if (val<0) val=0;$(' . $modeId
                . ').html(\'Manuell\');$(' . $tempId . ').html(val.toFixed(1)); $.get( \'' . $uri
                . '\' + val, function( data ) {});';
        }

        // control modes
        $modes = array(
            'Auto'      => array('setAutoMode', 1),
            'Boost'     => array('setBoostMode', 1),
            'Manuell'   => array('setTemperature', (float)$temp2),
            'Comfort'   => array('setComfortMode', 1),
            'ECO'       => array('setEcoMode', 1),
        );

        switch($mode)
        {
            case '1';
                $modeTxt = 'Manuell';
                break;
            case '2';
                $modeTxt = 'Party';
                break;
            case '3';
                $modeTxt = 'Boost';
                break;
            default:
        }

        $html .= '<span>';
        $html .= htmlentities($this->_config['label'], ENT_QUOTES, 'UTF-8');

        // Current temperature
        $html .= '<h1>';
        $html .= number_format((float)$temp1, 1, '.', '') . '°';
        $html .= '<span style="font-size: small" class="glyphicon glyphicon-' . $icon . '"></span>&nbsp;';

        // temperature up/down
        $html .= '<div class="btn-group-vertical">';
        $html .= '<button type="button" class="btn btn-default btn-sm' . $buttonStyle
            . '" onclick="' . $upScript . '"><span class="glyphicon glyphicon-arrow-up"></span></button>';
        $html .= '<button type="button" class="btn btn-default btn-sm' . $buttonStyle
            . '" onclick="' . $downScript . '"><span class="glyphicon glyphicon-arrow-down"></span></button>';
        $html .= '</div>&nbsp;';

        // control mode
        $html .= '<div class="btn-group">';
        $html .= '<button id="btnCtrlMode_' . $this->_id . '" type="button" class="btn btn-primary'
            . $buttonStyle . '">' . $modeTxt . '</button>';
        $html .= '<button type="button" class="btn btn-primary dropdown-toggle' . $buttonStyle
            . '" data-toggle="dropdown">
          <span class="caret"></span>
          <span class="sr-only">Toggle Dropdown</span>
        </button>';
        $html .= '<ul class="dropdown-menu" role="menu">';
        foreach ($modes as $key=>$value)
        {
            $onClick = 'void(0);';
            if ($value[0])
            {
                $uri = $this->_getAjaxUrl($value[0], array($this->_config['device_id'], $value[1]));
                $onClick = "$('#btnCtrlMode_" . $this->_id . "').html('" . $key . "');$.get( '"
                    . $uri . "', function( data ) {});";
            }
            $html .= '<li><a href="javascript:' . $onClick . '">' . $key . '</a></li>';
        }
        $html .= '</ul>';
        $html .= '</div>';
        $html .= '</h1>';

        $html .= '<small>Feuchte: ' . number_format((int)$valv) . '% / Soll: <span id="setTemp_' . $this->_id . '">'
            . number_format((float)$temp2, 1, '.', '') . '</span>°</small>';

        $html .= '</span>';

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
            $deviceId = $this->_config['device_id'];

            $script = $hm->getDeviceStatusScript('temp1' . $this->_id, $deviceId, 'ACTUAL_TEMPERATURE');
            $script .= $hm->getDeviceStatusScript('temp2' . $this->_id, $deviceId, 'SET_TEMPERATURE');
            $script .= $hm->getDeviceStatusScript('valv' . $this->_id, $deviceId, 'ACTUAL_HUMIDITY');
            $script .= $hm->getDeviceStatusScript('mode' . $this->_id, $deviceId, 'CONTROL_MODE');
        }

        return $script;
    }

    /**
     * Ajax action to set the temperature.
     *
     * @param array $params Array([device_id], [status])
     */
    public static function setTemperatureAjaxAction(array $params)
    {
        if (isset($params[1]) && is_numeric($params[1]))
        {
            $value = (float)$params[1];
            $hm = new Lib_Core_Homematic();
            $hm->setValue($params[0], 'MANU_MODE', $value);
        }
    }

    /**
     * Ajax action to set the mode to auto.
     *
     * @param array $params Array([device_id], [status])
     */
    public static function setAutoModeAjaxAction(array $params)
    {
        $hm = new Lib_Core_Homematic();
        $hm->setValue($params[0], 'AUTO_MODE', true);
    }

    /**
     * Ajax action to set the mode to eco.
     *
     * @param array $params Array([device_id], [status])
     */
    public static function setEcoModeAjaxAction(array $params)
    {
        $hm = new Lib_Core_Homematic();
        $hm->setValue($params[0], 'LOWERING_MODE', true);
    }

    /**
     * Ajax action to set the mode to comfort.
     *
     * @param array $params Array([device_id], [status])
     */
    public static function setComfortModeAjaxAction(array $params)
    {
        $hm = new Lib_Core_Homematic();
        $hm->setValue($params[0], 'COMFORT_MODE', true);
    }

    /**
     * Ajax action to set the mode to boost.
     *
     * @param array $params Array([device_id], [status])
     */
    public static function setBoostModeAjaxAction(array $params)
    {
        $hm = new Lib_Core_Homematic();
        $hm->setValue($params[0], 'BOOST_MODE', true);
    }
}
