<?php
/**
 * Homematic Service Messages Module.
 *
 * @package     Smarthome
 * @subpackage  Module
 * @author      Robert Gies <mail@rgies.com>
 * @copyright   Copyright © 2014 by Robert Gies
 * @license     New BSD License
 * @date        2014-01-10
 */

/**
 * Homematic Service Messages Class.
 */
class Module_Core_HomematicServiceMessages extends Module_Abstract
{
    /**
     * Required parameters for valid configuration.
     *
     * @var array
     */
    protected $_requiredParams = array('class', 'label');

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
        $messages = $hm->getServiceMessages();

        if ($messages && count($messages))
        {
            $html = htmlentities($this->_config['label']);

            if (mb_strpos($html, '%1') !== false)
            {
                $html = str_replace('%1', count($messages), $html);
            }
            else
            {
                $html .= ': ' . count($messages);
            }

            $message = '';
            foreach ($messages as $item)
            {
                $message .= htmlentities($this->_translateErrMessage($item[0], $item[1])) . '.<br/>';
            }

            $html .= '<!-- Button trigger modal -->
<button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#myModal' . $this->_id . '"> Details </button>

<!-- Modal -->
<div class="modal fade" id="myModal' . $this->_id . '" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title" id="myModalLabel">Service Messages</h4>
      </div>
      <div class="modal-body">
        ' . $message . '
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->';
        }

        return $html;
    }


    /**
     * Get error message translation.
     *
     * @param string $deviceId Id of hm device
     * @param string $message The error message
     * @return string Translated message
     */
    protected function _translateErrMessage($deviceId, $message)
    {
        $hm = new Lib_Core_Homematic();
        $dev = explode(':', $deviceId);
        $deviceList = $hm->getDeviceList();

        $deviceName = $deviceList[$dev[0]]['name'];

        switch($message)
        {
            case 'CONFIG_PENDING':
                $message = '';
                break;
            case 'LOWBAT':
                $message = 'Batteriestand niedrig';
                break;
            case 'STICKY_UNREACH':
                $message = 'Kommunikation war gestört';
                break;
            case 'UNREACH':
                $message = 'Kommunikation zur Zeit gestört';
                break;
        }

        return $deviceName . ': ' . $message;
    }
}