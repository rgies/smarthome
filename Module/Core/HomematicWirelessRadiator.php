<?php
/**
 * Homematic Wireless Radiator Module.
 *
 * @package     Smarthome
 * @subpackage  Module
 * @author      Robert Gies <mail@rgies.com>
 * @copyright   Copyright © 2014 by Robert Gies
 * @license     New BSD License
 * @date        2014-01-10
 */

/**
 * Homematic Wireless Radiator Class.
 */
class Module_Core_HomematicWirelessRadiator extends Module_Abstract
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
//        $hm = new Lib_Smarthome_Homematic();
//        //$ret = $hm->getParamsetDescription('KEQ0514679:4');
//        $s = $hm->getDeviceStatusScript($this->_id, $this->_config['device_id'],'ACTUAL_TEMPERATURE');
//        $ret = $hm->runScript($s);
//        print_r($s);
//        print_r ($vars); exit;

        // temperature
        $temp = (isset($vars['vartemp' . $this->_id])) ? $vars['vartemp' . $this->_id] : '';

        // valve state
        $valv = (isset($vars['varvalv' . $this->_id])) ? $vars['varvalv' . $this->_id] : '';


        $html .= '<span>';
        $html .= htmlentities($this->_config['label'], ENT_QUOTES, 'UTF-8');

        // Current temperature
        $html .= '<h1>' . number_format((float)$temp, 1, '.', '') . '°</h1>';

        $html .= '<small>Ventil: ' . $valv . '%</small>';

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
            $hm = new Lib_Smarthome_Homematic();
            $script = $hm->getDeviceStatusScript('temp' . $this->_id, $this->_config['device_id'],'ACTUAL_TEMPERATURE');
            $script .= $hm->getDeviceStatusScript('valv' . $this->_id, $this->_config['device_id'],'VALVE_STATE');
        }

        return $script;
    }

    public function setAutoModeAjaxAction()
    {
        //dom.GetObject('Name').DPByHssDP('AUTO_MODE').State(1);
    }
}