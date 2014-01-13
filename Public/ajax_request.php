<?php
/**
 * Smarthome Ajax Request Manager.
 *
 * @package     Smarthome
 * @author      Robert Gies <mail@rgies.com>
 * @copyright   Copyright © 2014 by Robert Gies
 * @license     New BSD License
 * @date        2014-01-10
 */

require_once '../Config/Init.php';

if (!isset($_REQUEST['module']))
{
    error_log('Ajax request: Module param missing !!!');
    exit;
}

$module = $_REQUEST['module'];
$action = $_REQUEST['action'];
$params = $_REQUEST['params'];

// Debug log
//error_log('Ajax request: ' . print_r($_REQUEST, true));

// Load config
$config = new Lib_Smarthome_Config();

if ($module == 'renderAlerts')
{
    // Render alerts
    echo Lib_Smarthome_HtmlHelper::renderAlerts($config->getAlerts());
}
elseif ($module == 'renderPanel')
{
    // Render panel
    $panel = $config->getPanel((int)$action-1, (int)$params-1);
    echo Lib_Smarthome_HtmlHelper::renderPanel($panel);
}
else
{
    // Call module method
    $functionName = $action . 'AjaxAction';
    echo $module::$functionName($params);
}
