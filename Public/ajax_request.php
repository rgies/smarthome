<?php
/**
 * Smarthome Ajax Request Manager.
 *
 * @package     Smarthome
 * @author      Robert Gies <mail@rgies.com>
 * @copyright   Copyright (c) 2014 by Robert Gies
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

if ($module == 'renderPanel')
{
    $html = 'test';

    // Load config
    $config = new Lib_Smarthome_Config();
    $panel = $config->getPanel((int)$action-1, (int)$params-1);


    if ($panel->children()->count())
    {
        echo '<ul class="list-group">';
        foreach ($panel->children() as $module)
        {
            echo '<li class="list-group-item">';

            $modConf = json_decode(json_encode((array)$module), 2);
            $modCls  = 'Module_' . (string)$module->class;
            $modObj = new $modCls($modConf);
            echo $modObj->renderHtml();

            echo '</li>';
        }
        echo '</ul>';
    }
    exit;
}

error_log('Ajax request: ' . print_r($_REQUEST, true));

$functionName = $action . 'AjaxAction';

// Call module method
echo $module::$functionName($params);