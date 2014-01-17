<?php
// =====================================================================================
// Config
// =====================================================================================

// ip address of homematic CCU
$homematicIp 	= '192.168.xxx.xxx';

// id of homematic Anwesenheits system variable
$atHomeSysVarId = '950';

// mobile devices to check
$deviceList 	= array(
    'iPhone xxx' => array('ip' => '192.168.xxx.xxx', 'var_id' => 'xxxx'),
    'iPhone xxx'  => array('ip' => '192.168.xxx.xxx', 'var_id' => 'xxxx'),
);

// delay for check out from home in seconds
$checkOutDelay = 1800;

// name of internal config file
$configFileName = __DIR__ . '/data.dat';
