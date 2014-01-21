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

/**
 * Load panel content by ajax call if not collapsed.
 *
 * @param id Id of panel
 * @param row Panel row number
 * @param col Panel column number
 */
function sm_updatePanelBody(id, row, col)
{
    if ($('#collapse' + id).hasClass('in'))
    {
        $('#panelBody' + id).html('');
    }
    else
    {
        $('#panelBody' + id).html('<div align="center"><img src="images/ajax-loader.gif"/></div>');
        uri = "ajax_request.php?module=renderPanel&action=" + row + "&params=" + col;
        $.get( uri, function( data ) { $('#panelBody' + id).html(data) });
    }
}
