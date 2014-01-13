<?php
/**
 * Smarthome Homematic Library.
 *
 * @package     Smarthome
 * @subpackage  Lib_Smarthome
 * @author      Robert Gies <mail@rgies.com>
 * @copyright   Copyright © 2014 by Robert Gies
 * @license     New BSD License
 * @date        2014-01-10
 */

require_once SH_ROOT_PATH . 'Lib/client.xmlrpc.php';
require_once SH_ROOT_PATH . 'Lib/client.json.php';
require_once SH_ROOT_PATH . 'Lib/client.http.php';

/**
 * Class Lib_Smarthome_Homematic.
 */
class Lib_Smarthome_Homematic
{
    protected static $_connection = array();

    protected static $_connectionErrorMessage;

    protected static $_host;

    protected $_port;


    public function __construct($port = 2001)
    {
        $this->_port = $port;

        if (!isset(self::$_connection[$port]))
        {
            // load config
            $config = new Lib_Smarthome_Config();

            // get xmlrpc connection
            self::$_connection[$port] = new client_xmlrpc(array('host'=>$config->getHost(), 'port'=>$port));
            self::$_host = $config->getHost();
        }

    }

    public function getValue($deviceId, $valueId)
    {
        return self::$_connection[$this->_port]->getValue($deviceId, $valueId);
    }

    public function setValue($deviceId, $valueId, $value)
    {
        return self::$_connection[$this->_port]->setValue($deviceId, $valueId, $value);
    }

    public function getState($varName)
    {
        $url = 'http://' . self::$_host . ':8181/rega.exe';
        $ret = @file_get_contents($url . '?state=dom.GetObject("' . rawurlencode($varName) . '").State()');

        if ($ret)
        {
            $xml = simplexml_load_string($ret);
            $state = $xml->state;

            switch($state)
            {
                case 'true':
                    $state = true;
                    break;
                case 'false':
                    $state = false;
                    break;
                case 'null':
                    $state = null;
                default:
                    $state = (string)$state;
            }

            return $state;
        }

        return null;
    }

    public function getDeviceStatusScript($id, $device, $value = 'state', $type = 'BidCos-RF')
    {
        $script = 'var' . $id . '=dom.GetObject("' . $type . '.' . $device . '.' . strtoupper($value) . '").State();';
        return $script;
    }

    public function getVarStatusScript($id, $variable)
    {
        $script = 'var' . $id . '=dom.GetObject("' . $variable . '").State();';
        return $script;
    }

    public function getServiceMessages()
    {
        if (self::$_connectionErrorMessage !== null)
        {
            return array();
        }
        return self::$_connection[$this->_port]->getServiceMessages();
    }

//    public function getStatus($deviceId)
//    {
//        $status = 0;
//
//        //$method = 'system.listMethods';
//        //$method = 'listDevices';
//
//        //$result = $api->listDevices();
//        //$result = $api->getDeviceDescription('KEQ0199286:1');
//        //$result = $api->getParamsetDescription('KEQ0199286:1', 'VALUES');
//        //print_r($result);
//        //$result = $api->getServiceMessages();
//        //$result = $api->$method();
//        //var_dump($result);
//        if (isset($deviceId) && $deviceId)
//        {
//            $status = self::$_connection[$this->_port]->getValue($deviceId, 'STATE');
//        }
//
//        return $status;
//    }

    public function getParamsetDescription($deviceId)
    {
        $ret = '';

        if (isset($deviceId) && $deviceId)
        {
            $ret = self::$_connection[$this->_port]->getParamsetDescription($deviceId, 'VALUES');
        }

        return $ret;
    }

    /**
     * Check if connection to CCU is working.
     *
     * @param int $timeout Timeout in seconds
     * @return bool|string True or error message
     */
    public function checkConnection($timeout = 2)
    {
        $ret = true;

        $fp = @fsockopen (self::$_host, 8181, $errno, $errstr, $timeout);
        if (!$fp)
        {
            $ret = '<strong>Connection Error:</strong> Die Homematic CCU kann unter der Netzwerkadresse [' . self::$_host
                . '] nicht erreicht werden. Bitte prüfen Sie die Netzwerkverbindung und die Config.xml Konfigurationsdatei.';
            self::$_connectionErrorMessage = $ret;
            error_log($ret);
        }
        else
        {
            fclose($fp);
            self::$_connectionErrorMessage = null;
        }

        return $ret;
    }

    public function runScript($script)
    {
        if (!$script || self::$_connectionErrorMessage !== null)
        {
            return array();
        }

        $fp = @fsockopen (self::$_host, 8181, $errno, $errstr, 2);
        $res = '';
        $xml = '';

        if (!$fp)
        {
            $res = '<xml><error>' . utf8_encode($errstr) . '</error></xml>';
            error_log($res);
        }
        else
        {
            // Zusammenstellen des Header für HTTP-Post
            fputs($fp, "POST /Test.exe HTTP/1.1\r\n");
            fputs($fp, "Content-type: application/x-www-form-urlencoded\r\n");
            fputs($fp, "Content-length: ". strlen($script) ."\r\n");
            fputs($fp, "Connection: close\r\n\r\n");
            fputs($fp, $script);
            while(!feof($fp))
            {
                $res .= fgets($fp, 500);
            }
            fclose($fp);
        }

        $pos = mb_strpos($res, '<xml>');
        if ($pos !== false)
        {
            $xml = simplexml_load_string(mb_substr($res, $pos));
            $xml = json_decode(json_encode((array)$xml), 2);
        }

        return $xml;
    }

}