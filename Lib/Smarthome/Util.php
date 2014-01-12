<?php
/**
 * Smarthome Util.
 *
 * @package     Smarthome
 * @subpackage  Lib_Smarthome
 * @author      Robert Gies <mail@rgies.com>
 * @copyright   Copyright © 2014 by Robert Gies
 * @license     New BSD License
 * @date        2014-01-10
 */

/**
 * Class Lib_Smarthome_Util.
 */
class Lib_Smarthome_Util
{
    /**
     * Gets html code from given module name.
     *
     * @param object $module SimpleXml object of module config
     * @return string Html
     */
    public static function renderModule($module)
    {
        $moduleConf = json_decode(json_encode((array)$module), 2);
        $moduleClass = 'Module_' . (string)$module->class;
        $moduleObject = new $moduleClass($moduleConf);
        return $moduleObject->renderHtml();
    }

    /**
     * Gets script code from given module name.
     *
     * @param object $module SimpleXml object of module config
     * @return string Script
     */
    public static function renderScript($module)
    {
        $moduleConf = json_decode(json_encode((array)$module), 2);
        $moduleClass = 'Module_' . (string)$module->class;
        $moduleObject = new $moduleClass($moduleConf);
        return $moduleObject->getRequestScript();
    }

    /**
     * Renders alerts from given alerts config object.
     *
     * @param $alerts SimpleXml object of module config
     * @return string Html
     */
    static public function renderAlerts($alerts)
    {
        $html = '';
        $script = '';
        $modules = array();

        if ($alerts)
        {
            $hm = new Lib_Smarthome_Homematic();

            foreach ($alerts as $alert)
            {
                $module = $alert->module;
                $moduleConf = json_decode(json_encode((array)$module), 2);
                $moduleClass = 'Module_' . (string)$module->class;
                $moduleObject = new $moduleClass($moduleConf);
                $modules[] = $moduleObject;
                $script .= $moduleObject->getRequestScript();
            }

            $vars = $hm->runScript($script);

            $z = 0;
            foreach ($alerts as $alert)
            {
                $module = $modules[$z];
                if ($ret = $module->renderHtml($vars))
                {
                    $html .= '<div class="alert alert-' . $alert['type'] . '">' . $ret . '</div>';
                }
                $z++;
            }
        }
        return $html;
    }

    /**
     * Renders html code for all module at given panel.
     *
     * @param object $panel SimpleXml object of panel config
     * @return string Html
     */
    public static function renderPanel($panel)
    {
        $html = '';
        $script = '';
        $modules = array();

        if ($panel->children()->count())
        {
            foreach ($panel->children() as $module)
            {
                $moduleConf = json_decode(json_encode((array)$module), 2);
                $moduleClass = 'Module_' . (string)$module->class;
                $moduleObject = new $moduleClass($moduleConf);
                $modules[] = $moduleObject;
                $script .= $moduleObject->getRequestScript();
            }

            $hm = new Lib_Smarthome_Homematic();
            $vars = $hm->runScript($script);

            $html .= '<ul class="list-group">';
            foreach ($modules as $module)
            {
                if ($ret = $module->renderHtml($vars))
                {
                    $html .= '<li class="list-group-item">' . $ret . '</li>';
                }
            }
            $html .= '</ul>';
        }
        return $html;
    }
}