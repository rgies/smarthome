<?php
/**
 * Homematic Variable Module.
 *
 * @package     Smarthome
 * @subpackage  Module
 * @author      Robert Gies <mail@rgies.com>
 * @copyright   Copyright © 2014 by Robert Gies
 * @license     New BSD License
 * @date        2014-01-10
 */

/**
 * Homematic Variable Class.
 */
class Module_Core_HomematicVariable extends Module_Abstract
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
        $hm = new Lib_Core_Homematic();

        //$value = $hm->getState($this->_config['variable']);
        $value = (isset($vars['var' . $this->_id])) ? $vars['var' . $this->_id] : '';

        if ($value == 'true')
        {
            $value = true;
        }
        elseif ($value == 'false')
        {
            $value = false;
        }

        // Map value to the right content
        if (isset($this->_config['values']))
        {
            $values = explode(',', $this->_config['values']);

            if (is_bool($value))
            {
                $value = ($value) ? $value = $values[1] : $value = $values[0];
            }
            elseif (is_numeric($value) && isset($values[$value]))
            {
                $value = $values[$value];
            }
        }

        // Do not show anything if configured
        if (isset($this->_config['hide']))
        {
            $hides = explode(',', $this->_config['hide']);
            if (in_array($value, $hides) || $value == '')
            {
                return '';
            }
        }

        $html .= '<span>';
        $html .= htmlentities($this->_config['label'], ENT_QUOTES, 'UTF-8') . ': '
            . htmlentities($value, ENT_QUOTES, 'UTF-8');
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

        if (isset($this->_config['variable']) && $this->_config['variable'])
        {
            $hm = new Lib_Core_Homematic();
            $script = $hm->getVarStatusScript($this->_id, $this->_config['variable']);
        }

        return $script;
    }
}