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
     * Required parameters for valid configuration.
     *
     * @var array
     */
    protected $_requiredParams = array('class', 'label', 'variable');

    /**
     * Gets html code shown in configured panel.
     *
     * @var array vars Variables from ccu response
     * @return string Html code
     */
    public function renderHtml($vars = array())
    {
        $html = '';

        $value = (isset($vars['var' . $this->_id])) ? $vars['var' . $this->_id] : null;

        if ($value == 'true')
        {
            $value = true;
        }
        elseif ($value == 'false')
        {
            $value = false;
        }
        elseif (is_array($value))
        {
            $value = '';
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

        // if variable can be set
        if (isset($this->_config['changeable']))
        {
            $content = '&nbsp;<div class="btn-group">'
                . '<button id="btnHeizMode_' . $this->_id . '" type="button" class="btn btn-xs btn-primary">'
                . htmlentities($value, ENT_QUOTES, 'UTF-8')
                . '</button>';
            $content .= '<button type="button" class="btn btn-xs btn-primary dropdown-toggle" data-toggle="dropdown">
                <span class="caret"></span>
                <span class="sr-only">Toggle Dropdown</span>
                </button>';
            $content .= '<ul class="dropdown-menu" role="menu">';

            $z=0;
            foreach (explode(',', $this->_config['values']) as $select)
            {
                $token = md5(__CLASS__ . $z . Lib_Core_Config::$secret . $this->_config['label']);
                $uri = $this->_getAjaxUrl('setVar', array($this->_config['label'], $z, $token));

                $onClick = "$('#btnHeizMode_" . $this->_id . "').html('" . $select . "');$.get( '"
                    . $uri . "', function( data ) {});";

                $content .= '<li><a href="javascript: ' . $onClick .'">'
                    . htmlentities($select, ENT_QUOTES, 'UTF-8')
                    . '</a></li>';
                $z++;
            }
            $content .= '</ul></div>';
        }
        else
        {
            $content = htmlentities($value, ENT_QUOTES, 'UTF-8');
        }

        $html .= '<span>';
        $html .= htmlentities($this->_config['label'], ENT_QUOTES, 'UTF-8') . ': ' . $content;
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

    /**
     * Sets Variable to given value.
     *
     * @param array $params
     */
    public static function setVarAjaxAction(array $params)
    {
        if (isset($params[0]) && isset($params[1]) && isset($params[2]))
        {
            $token = md5(__CLASS__ . $params[1] . Lib_Core_Config::$secret . $params[0]);
            if ($token != $params[2])
            {
                die('Access denied!');
            }

            $config = new Lib_Core_Config();
            $modConf = $config->getModuleByLabel($params[0]);

            if (count($modConf) && isset($modConf[0]->changeable))
            {
                $hm = new Lib_Core_Homematic();
                $hm->setState((string)$modConf[0]->variable, (string)$params[1]);
            }
        }

    }
}