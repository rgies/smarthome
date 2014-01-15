<?php
/**
 * Smarthome Html Helper.
 *
 * @package     Smarthome
 * @subpackage  Lib_Smarthome
 * @author      Robert Gies <mail@rgies.com>
 * @copyright   Copyright © 2014 by Robert Gies
 * @license     New BSD License
 * @date        2014-01-10
 */

/**
 * Class Lib_Core_HtmlHelper.
 */
class Lib_Core_HtmlHelper
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
            $hm = new Lib_Core_Homematic();

            foreach ($alerts as $alert)
            {
                $module = $alert->module;
                $moduleConf = json_decode(json_encode((array)$module), 2);
                $moduleClass = 'Module_' . (string)$module->class;
                $moduleObject = new $moduleClass($moduleConf);
                $modules[] = $moduleObject;
                $script .= $moduleObject->getRequestScript();
            }

            // execute homematic script with max 3 retry loops on problems
            $vars = $hm->runScript($script, 3);

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
            $hm = new Lib_Core_Homematic();

            foreach ($panel->children() as $module)
            {
                $moduleConf = json_decode(json_encode((array)$module), 2);
                $moduleClass = 'Module_' . (string)$module->class;
                $moduleObject = new $moduleClass($moduleConf);
                $modules[] = $moduleObject;
                $script .= $moduleObject->getRequestScript();
            }

            // execute homematic script with max 3 retry loops on problems
            $vars = $hm->runScript($script, 3);

            $html .= '<ul class="list-group">';
            foreach ($modules as $module)
            {
                if ($ret = $module->renderHtml($vars))
                {
                    //$html .= '<li class="list-group-item" style="float: left; width: 250px;">' . $ret . '</li>';
                    $html .= '<li class="list-group-item">' . $ret . '</li>';
                    //$html .= '<li class="list-group-item col-xs-6 col-md-6">' . $ret . '</li>';
                }
            }
            $html .= '</ul>';
        }
        return $html;
    }

    /**
     * Get javascript code to refresh the alerts.
     *
     * @return string Js code
     */
    public static function renderAlertRefreshJs()
    {
        $refreshTime = 60;
        $js = '';

        $uri = 'ajax_request.php?module=renderAlerts&action&params';
        $jsTimeout = 'setTimeout("refresh_Alerts()", ' . $refreshTime * 1000 . ');';
        $jsScript = 'if (!$(".modal").hasClass("in")){';
        $jsScript .= '$.get( "' . $uri . '", function( data ) { $( "#alertBody" ).html( data ); });';
        $jsScript .= '};';
        $js .= 'function refresh_Alerts(){' . $jsScript . $jsTimeout . '};';
        $js .= $jsTimeout;

        return $js;
    }

    /**
     * Get javascript code to refresh the panels.
     *
     * @param array $panels Panels array
     * @return string Js code
     */
    public static function renderPanelRefreshJs($panels)
    {
        $js = '';
        $z=0;
        foreach ($panels as $id=>$panel)
        {
            $z++;
            if (isset($panel[2]['refresh']) && is_numeric((int)$panel[2]['refresh']))
            {
                $refreshTime = ((int)$panel[2]['refresh'] < 10) ? 10 : (int)$panel[2]['refresh'];
                $uri = 'ajax_request.php?module=renderPanel&action=' . $panel[0] . '&params=' . $panel[1];
                $jsTimeout = 'setTimeout("refresh_' . $id . '()", ' . $refreshTime * 1000 . ');';
                $jsScript = 'if (!$(".modal").hasClass("in") && $("#collapse' . $z . '").hasClass("in")){';
                $jsScript .= '$.get( "' . $uri . '", function( data ) { $( "#' . $id . '" ).html( data ); });';
                $jsScript .= '};';
                $js .= 'function refresh_' . $id . '(){' . $jsScript . $jsTimeout . '};';
                $js .= $jsTimeout;
            }
        }
        return $js;
    }
}