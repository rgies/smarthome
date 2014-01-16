/**
 * Smarthome Javascipt Library.
 *
 * @package     Smarthome
 * @subpackage  Lib_Smarthome
 * @author      Robert Gies <mail@rgies.com>
 * @copyright   Copyright © 2014 by Robert Gies
 * @license     New BSD License
 * @date        2014-01-10
 */

function sm_updatePanelBody(id, row, col)
{
    if ($('#panelBody' + id).html() == '')
    {
        $('#panelBody' + id).html('<div align="center"><img src="images/ajax-loader.gif"/></div>');
        uri = "ajax_request.php?module=renderPanel&action=" + row + "&params=" + col;
        $.get( uri, function( data ) { $('#panelBody' + id).html(data) });
    }
    else
    {
        $('#panelBody' + id).html('');
    }
}