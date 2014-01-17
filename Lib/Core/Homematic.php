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
 * Class Lib_Core_Homematic.
 */
class Lib_Core_Homematic
{
    protected static $_connection = array();

    protected static $_connectionErrorMessage;

    protected static $_host;

    protected $_port;

    protected static $_devices;


    public function __construct($port = 2001)
    {
        $this->_port = $port;

        if (!isset(self::$_connection[$port]))
        {
            // load config
            $config = new Lib_Core_Config();

            // get xmlrpc connection
            self::$_connection[$port] = new client_xmlrpc(array('host'=>$config->getHost(), 'port'=>$port));
            self::$_host = $config->getHost();
        }

        // load homematic device list
        if (null === self::$_devices)
        {
            self::$_devices = $this->_getDeviceList();
        }
    }

    /**
     * Get device list of all known homematic components.
     *
     * @return array Array of devices with address as key
     * @throws Exception If can not get list
     */
    protected function _getDeviceList()
    {
        if (is_file(SH_ROOT_PATH . 'Data/devices.dat'))
        {
            $devices = unserialize(file_get_contents(SH_ROOT_PATH . 'Data/devices.dat'));
        }
        else
        {
            $script = 'string s_object_id; object o; string v1; '
                . 'foreach (s_object_id, dom.GetObject(ID_DEVICES).EnumUsedIDs()){'
                . 'o = dom.GetObject (s_object_id); v1 = v1 + o.Address() + "|" + o.HssType() + "|" + o.Name() + "\n";'
                . '}';

            $xml = $this->_runScript($script);

            if (!isset($xml['v1']))
            {
                throw new Exception('Homematic device list can not be load.');
            }

            $ret = $xml['v1'];
            $ret = explode("\n", $ret);

            $devices = array();
            foreach ($ret as $item)
            {
                if ($item)
                {
                    $item = explode('|', $item);
                    $devices[$item[0]] = array('type' => $item[1],'name' => $item[2]);
                }
            }

            file_put_contents(SH_ROOT_PATH . 'Data/devices.dat', serialize($devices));
        }

        return $devices;
    }

    /**
     * Get device list of all known homematic components.
     *
     * @return array Array of devices with address as key
     * @throws Exception If can not get list
     */
    public function getDeviceList()
    {
        return self::$_devices;
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

    /**
     * Execute given homematic script.
     *
     * @param string $script Homematic script to execute
     * @param integer $retries Number of retries on problems
     * @return array Execution result array
     */
    public function runScript($script, $retries = 0)
    {
        for ($i=0; $i<=$retries; $i++)
        {
            $vars = $this->_runScript($script);
            if (Lib_Core_FunctionHelper::validateScriptResult($vars))
            {
                continue;
            }
            error_log('Retry script execution in ' . __CLASS__ . ':' . $script);
        }

        return $vars;
    }

    /**
     * Execute given homematic script.
     *
     * @param string $script Homematic script to execute
     * @return array Execution result array
     */
    protected function _runScript($script)
    {
        if (!$script || self::$_connectionErrorMessage !== null)
        {
            return array();
        }

        $fp = @fsockopen (self::$_host, 8181, $errno, $errstr, 2);
        $res = '';
        $xml = array();

        if (!$fp)
        {
            error_log(utf8_encode($errstr));
            return array('<error>' => utf8_encode($errstr));
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
            $xml = simplexml_load_string(utf8_encode(mb_substr($res, $pos)));
            $xml = json_decode(json_encode((array)$xml), 2);
        }

        return $xml;
    }

}