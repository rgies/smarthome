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

        // control modes
        $modes = array('Auto', 'Manuell', 'Comfort', 'ECO', 'Boost');
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
                $modeTxt = 'Auto';
        }


        $html .= '<span>';
        $html .= htmlentities($this->_config['label'], ENT_QUOTES, 'UTF-8');

        // Current temperature
        $html .= '<h1>';
        $html .= number_format((float)$temp1, 1, '.', '') . '°';
        $html .= '<span style="font-size: small" class="glyphicon glyphicon-' . $icon . '"></span>&nbsp;';

        $html .= '<div class="btn-group-vertical">';
        $html .= '<button type="button" class="btn btn-default btn-sm"><span class="glyphicon glyphicon-arrow-up"></span></button>';
        $html .= '<button type="button" class="btn btn-default btn-sm"><span class="glyphicon glyphicon-arrow-down"></span></button>';
        $html .= '</div>&nbsp;';

        $html .= '<div class="btn-group">';
        $html .= '<button type="button" class="btn btn-primary">' . $modeTxt . '</button>';
        $html .= '<button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown">
          <span class="caret"></span>
          <span class="sr-only">Toggle Dropdown</span>
        </button>';
        $html .= '<ul class="dropdown-menu" role="menu">';
        foreach ($modes as $key=>$value)
        {
            $html .= '<li><a href="#">' . $value . '</a></li>';
        }
        $html .= '</ul>';
        $html .= '</div>';
        $html .= '</h1>';

        $html .= '<small>Ventil: ' . $valv . '% / Soll: ' . number_format((float)$temp2, 1, '.', '') . '°</small>';

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
            $script = $hm->getDeviceStatusScript('temp1' . $this->_id, $this->_config['device_id'],'ACTUAL_TEMPERATURE');
            $script .= $hm->getDeviceStatusScript('temp2' . $this->_id, $this->_config['device_id'],'SET_TEMPERATURE');
            $script .= $hm->getDeviceStatusScript('valv' . $this->_id, $this->_config['device_id'],'VALVE_STATE');
            $script .= $hm->getDeviceStatusScript('mode' . $this->_id, $this->_config['device_id'],'CONTROL_MODE');
        }

        return $script;
    }

    public function setAutoModeAjaxAction()
    {
        //dom.GetObject('Name').DPByHssDP('AUTO_MODE').State(1);
        //dom.GetObject("BidCos-RF.KEQ0431880:4.MANU_MODE").State(22.5);
    }

    /*
===========================================
CONTROL_MODE:
Type: enum
Wertebereich:
<string>AUTO-MODE</string>
<string>MANU-MODE</string>
<string>PARTY-MODE</string>
<string>BOOST-MODE</string>
Default: 0 -> Auto
Operations: 5 -> Read, Event
===========================================
FAULT_REPORTING:
Type: ENUM
Wertebereich:
<string>NO FAULT</string>
<string>VALVE TIGHT</string>
<string>ADJUSTING RANGE TOO LARGE</string>
<string>ADJUSTING RANGE TOO SMALL</string>
<string>COMMUNICATION ERROR</string>
<string /> // Das ist der Default-Wert
<string>LOWBAT</string>
<string>VALVE ERROR POSITION</string>
Default: 5 -> der leere String
Operations: 5: Read, Event
===========================================
BATTERY_STATE:
Type: Float
Wertebereich: (V)
Min: 1.5
Max: 4.6
Default: 0
Operations: 5 -> Read, Event
===========================================
VALVE_STATE:
Type: integer
Wertebereich: (%)
Min: 0
Max: 99
Default: 0
Operations: 5 -> Read, Event
===========================================
ACTUAL_TEMPERATURE:
Type: FLOAT
Wertebereich: (Grad C)
Min: -10
Max: 50
Default: 0
Operations: 1 -> Read
===========================================
SET_TEMPERATURE:
Type: FLOAT
Wertebereich: (Grad C)
Default: 20
Operations: 7 -> Read, Write, Event
===========================================
AUTO_MODE:
Type: Boolean -> Action -> Nur True
Wertebereich:
True:
False:
Default: false
Operations: 2 -> Write
===========================================
MANU_MODE:
Type: Float
Wertebereich: Grad C)
Min: 4.5
Max: 30.5
Default: 20
Operations: 2 -> Write
===========================================
BOOST_MODE:
Type: Boolean
Type: Boolean -> Action -> Nur True
TRUE
FALSE
Default: False
Operations: 2 -> Read
===========================================
COMFORT_MODE:
Type: Boolean -> Action -> Nur True
Wertebereich:
TRUE:
FALSE:
Default: false
Operations: 2 -> Read
===========================================
LOWERING_MODE:
Type: Boolean -> Action -> Nur True
Wertebereich:
TRUE
FALSE
Default: FALSE
Operations: 2 -> READ
===========================================
PARTY_MODE:
Type: Float
Wertebereich: Grad C)
Min: 5
Max: 30
Default: 20
Operations: 2 -> Write
===========================================
PARTY_START_DAY:
Type: integer
Wertebereich: (day)
Min:1
Max:31
Default: 1
Operations: 2 -> Write
===========================================
PARTY_START_MONTH:
Type integer
Wertebereich: (month)
Min:1
Max:12
Default:1
Operations: 2 -> Write
===========================================
PARTY_START_YEAR:
Type: integer
Wertebereich:
Min:0
Max:99
Default:12
Operations: 2 -> Write
===========================================
PARTY_START_TIME:
Type: integer
Wertebereich: (minutes)
Min:0
Max:1440
Default:0
Operations: 2 -> Write
===========================================
PARTY_STOP_DAY:
Type: integer
Wertebereich: (day)
Min:1
Max:31
Default: 1
Operations: 2 -> Write
===========================================
PARTY_STOP_MONTH:
Type integer
Wertebereich: (month)
Min:1
Max:12
Default:1
Operations: 2 -> Write
===========================================
PARTY_STOP_YEAR:
Type: integer
Wertebereich:
Min:0
Max:99
Default:12
Operations: 2 -> Write
===========================================
PARTY_STOP_TIME:
Type: integer
Wertebereich: (minutes)
Min:0
Max:1440
Default:0
Operations: 2 -> Write
===========================================
     */
}