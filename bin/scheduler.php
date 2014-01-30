<?php
/**
 * Script to find connected mobile devices to check presence of persons.
 *
 * To find out homematic device ids call:
 * http://[homematic IP]/config/xmlapi/sysvarlist.cgi
 *
 * @author  Robert Gies <mail@rgies.de>
 * @copyright 2013 by Robert Gies
 */

// =====================================================================================
// Config
// =====================================================================================
require_once (__DIR__ . '/config.php');
// =====================================================================================


/**
 * Ping command to check connectivity
 *
 * @param string $host
 * @param int $timeout
 * @return bool|mixed
 */
function ping($host, $timeout = 1)
{
    /* ICMP ping packet with a pre-calculated checksum */
    $package = "\x08\x00\x7d\x4b\x00\x00\x00\x00PingHost";
    $socket  = socket_create(AF_INET, SOCK_RAW, 1);
    socket_set_option($socket, SOL_SOCKET, SO_RCVTIMEO, array('sec' => $timeout, 'usec' => 0));
    socket_connect($socket, $host, null);

    $ts = microtime(true);
    socket_send($socket, $package, strLen($package), 0);
    if (socket_read($socket, 255))
        $result = microtime(true) - $ts;
    else
        $result = false;
    socket_close($socket);

    return $result;
}

// load config file
$config = array();
if (is_file($configFileName))
{
    $config = unserialize(file_get_contents($configFileName));
}
if (!isset($config['atHome']))
{
    $config['atHome'] = '';
}

$atHome = null;

// loop to all defined devices
foreach ($deviceList as $name=>$item)
{
    $ret = ping($item['ip']);

    // init config section
    if (!isset($config[$name]))
    {
        $config[$name] = array();
    }
    if (!isset($config[$name]['timestamp']))
    {
        $config[$name]['timestamp'] = time() - $checkOutDelay;
    }
    if (!isset($config[$name]['state']))
    {
        $config[$name]['state'] = 0;
    }

    if ($ret !== false)
    {
        $state = 1;
        $atHome = 1;
    }
    elseif ($config[$name]['state'] == 0)
    {
        continue;
    }
    else
    {
        $state = 0;

        // checkout after end of defined delay
        if (time() - $config[$name]['timestamp'] < $checkOutDelay)
        {
            continue;
        }

        if ($atHome === null)
        {
            $atHome = 0;
        }
    }

    // set homematic variable
    if (isset($item['var_id']) && $config[$name]['state'] != $state)
    {
        $xml = @file_get_contents('http://' . $homematicIp . ':8181/rega.exe?state=dom.GetObject("'
            . rawurlencode($item['var_id']) . '").State(' . $state . ')');

        // remember data
        $config[$name]['state'] = $state;
    }

    $config[$name]['timestamp'] = time();

}

// set Anwesenheit system variable
if ($atHome !== null && $config['atHome'] != $atHome)
{
    $xml = @file_get_contents('http://' . $homematicIp . ':8181/rega.exe?state=dom.GetObject("'
        . rawurlencode($atHomeSysVarId) . '").State(' . $atHome . ')');

    $config['atHome'] = $atHome;
}

// save config file
file_put_contents($configFileName, serialize($config));
