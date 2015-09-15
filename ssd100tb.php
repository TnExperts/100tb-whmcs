<?php

//TODO: add power status on product overview page.
//TODO: add reset password client option.
//TODO: on admin password change update in tera.

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

require_once("class.api.php");

function ssd100tb_MetaData()
{
    return array(
        'DisplayName' => '100TB SSD VPS'
    );
}

function getTemplateId($locationId, $index)
{
    $locationId = (int) $locationId;
    $index = (int) $index;

    //New York City Templates
    $templates2 = array(
        0 => array(
            'id' => 19,
            'label' => 'CentOS 6.6 x64 cPanel',
            'min_disk_size' => 9,
            'min_memory_size' => 384,
            'operating_system' => 'linux',
            'operating_system_arch' => 'x64',
        ),
        1 => array(
            'id' => 9,
            'label' => 'CentOS 6.7 x64',
            'min_disk_size' => 5,
            'min_memory_size' => 384,
            'operating_system' => 'linux',
            'operating_system_arch' => 'x64',
        ),
        2 => array(
            'id' => 10,
            'label' => 'CentOS 7.1 x64',
            'min_disk_size' => 5,
            'min_memory_size' => 384,
            'operating_system' => 'linux',
            'operating_system_arch' => 'x64',
        ),
        3 => array(
            'id' => 12,
            'label' => 'Debian 6.0 x64',
            'min_disk_size' => 5,
            'min_memory_size' => 128,
            'operating_system' => 'linux',
            'operating_system_arch' => 'x64',
        ),
        4 => array(
            'id' => 13,
            'label' => 'Debian 7.0 x64',
            'min_disk_size' => 5,
            'min_memory_size' => 128,
            'operating_system' => 'linux',
            'operating_system_arch' => 'x64',
        ),
        5 => array(
            'id' => 20,
            'label' => 'Ubuntu 14.04 x64',
            'min_disk_size' => 5,
            'min_memory_size' => 256,
            'operating_system' => 'linux',
            'operating_system_arch' => 'x64',
        ),
        6 => array(
            'id' => 25,
            'label' => 'Windows 2008 x64 STD R2',
            'min_disk_size' => 20,
            'min_memory_size' => 1024,
            'operating_system' => 'windows',
            'operating_system_arch' => 'x64',
        ),
        7 => array(
            'id' => 26,
            'label' => 'Windows 2012 x64 STD R2',
            'min_disk_size' => 20,
            'min_memory_size' => 1024,
            'operating_system' => 'windows',
            'operating_system_arch' => 'x64',
        ),
    );

    //Salt lake city templates
    $templates3 = array (
        0 => array(
            'id' => 13,
            'label' => 'CentOS 6.6 cPanel x64',
            'min_disk_size' => 9,
            'min_memory_size' => 384,
            'operating_system' => 'linux',
            'operating_system_arch' => 'x64',
        ),
        1 => array(
            'id' => 21,
            'label' => 'CentOS 6.7 x64',
            'min_disk_size' => 5,
            'min_memory_size' => 384,
            'operating_system' => 'linux',
            'operating_system_arch' => 'x64',
        ),
        2 => array(
            'id' => 44,
            'label' => 'CentOS 7.1 x64',
            'min_disk_size' => 5,
            'min_memory_size' => 384,
            'operating_system' => 'linux',
            'operating_system_arch' => 'x64',
        ),
        3 => array(
            'id' => 30,
            'label' => 'Debian 6.0 x64',
            'min_disk_size' => 5,
            'min_memory_size' => 128,
            'operating_system' => 'linux',
            'operating_system_arch' => 'x64',
        ),
        4 => array(
            'id' => 28,
            'label' => 'Debian 7.0 x64',
            'min_disk_size' => 5,
            'min_memory_size' => 128,
            'operating_system' => 'linux',
            'operating_system_arch' => 'x64',
        ),
        5 => array(
            'id' => 18,
            'label' => 'Ubuntu 14.04 x64',
            'min_disk_size' => 5,
            'min_memory_size' => 256,
            'operating_system' => 'linux',
            'operating_system_arch' => 'x64',
        ),
        6 => array(
            'id' => 32,
            'label' => 'Windows 2008 x64 STD R2',
            'min_disk_size' => 20,
            'min_memory_size' => 1024,
            'operating_system' => 'windows',
            'operating_system_arch' => 'x64',
        ),
        7 => array(
            'id' => 42,
            'label' => 'Windows 2012 x64 STD R2',
            'min_disk_size' => 20,
            'min_memory_size' => 1024,
            'operating_system' => 'windows',
            'operating_system_arch' => 'x64',
        ),
    );

    if ($locationId === 3 && $index < 8) {
        return (int) $templates3[$index]['id'];
    } else if ($locationId === 2 && $index < 8) {
        return (int) $templates2[$index]['id'];
    }

    return 0;
}

function ssd100tb_ConfigOptions()
{
    if(!mysql_num_rows(mysql_query("SHOW TABLES LIKE 'mod_100tb'"))) {
        $query = '
			CREATE TABLE IF NOT EXISTS `mod_100tb_vps` (
				 `serviceid` int(11) unsigned NOT NULL,
				 `serverid` int(11) unsigned NOT NULL,
				 `alternateApiKey` VARCHAR( 255 ) DEFAULT NULL,
				 PRIMARY KEY (`serviceid`),
				 UNIQUE KEY `serverid` (`serverid`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8';

        mysql_query($query);
    }

    $configArray = array(
        'Plan' => array(
            'Type' => 'dropdown',
            'Options' => array(
                '2446' => '1 Core / 0.5GB RAM / 25GB SSD / 2TB Bandwidth - $5.00',
                '2447' => '1 Core / 1GB RAM / 35GB SSD / 3TB Bandwidth - $10.00',
                '2448' => '2 Cores / 2GB RAM / 45GB SSD / 4TB Bandwidth - $20.00',
                '2449' => '2 Cores / 4GB RAM / 70GB SSD / 5TB Bandwidth - $38.00',
                '2450' => '4 Cores / 8GB RAM / 90GB SSD / 6TB Bandwidth - $74.00',
                '2451' => '4 Cores / 16GB RAM / 120GB SSD / 7TB Bandwidth - $140.00',
                '2452' => '8 Cores / 32GB RAM / 250GB SSD / 8TB Bandwidth - $260.00',
                '2453' => '8 Cores / 48GB RAM / 500GB SSD / 9TB Bandwidth - $450.00',
                '2454' => '16 Cores / 64GB RAM / 750GB SSD / 10TB Bandwidth - $590.00',
                '2455' => '20 Cores / 128GB RAM / 900GB SSD / 20TB Bandwidth - $720.00'
            ),
            'Description' => 'Choose one',
        ),
        'Backups' => array(
            'Type' => 'yesno',
            'Description' => 'Tick to enable ($5.00)',
        ),
        'Location' => array(
            'Type' => 'dropdown',
            'Options' => array(
                '3' => 'Salt Lake City, UT, US', //3 for live server
                '2' => 'New York City, New York, US', //2 for live server
            ),
            'Description' => 'Choose one',
        ),
        'API Key' => array(
            'Type' => 'text',
            'Size' => '30',
            'Description' => 'https://console.100tb.com/#/tools/api',
        ),
        'Template' => array(
            'Type' => 'dropdown',
            'Options' => array(
                '0' => 'CentOS 6.6 x64 cPanel - $10.00',
                '1' => 'CentOS 6.7 x64 - $0.00',
                '2' => 'CentOS 7.1 x64 - $0.00',
                '3' => 'Debian 6.0 x64 - $0.00',
                '4' => 'Debian 7.0 x64 - $0.00',
                '5' => 'Ubuntu 14.04 x64 - $0.00',
                '6' => 'Windows 2008 x64 STD R2 - $7.50',
                '7' => 'Windows 2012 x64 STD R2 - $7.50',
            ),
            'Description' => 'Choose one',
        ),

    );

    return $configArray;
}

/**
 * $params['configoption1'] = planId (int)
 * $params['configoption2'] = backups (bool)
 * $params['configoption3'] = locationId (int)
 * $params['configoption4'] = api_key (str)
 * $params['configoption5'] = Template (int)
**/
function ssd100tb_CreateAccount(array $params)
{
    $templateId = getTemplateId($params['configoption3'], $params['configoption5']);

    try {
        $API = new API($params['configoption4']);

        $response = $API->post('vps.json',array(
            'planId' => (int) $params['configoption1'],
            'locationId' => (int) $params['configoption3'],
            'templateId' => $templateId, //template id must be parsed by location above
            'label' => $params['domain'],
            'hostname' => $params['domain'],
            'password' => $params['password'],
            'backups' => ($params['configoption2'] === 'on'),
            'billHourly' => false
        ));

        if (isset($response['server'])) {
            update_query("tblhosting", array(
                "server" => (int) $response['server'],
                "username" => ($params['configoption5'] == 6 || $params['configoption5'] == 7)?'administrator':'root',
                "dedicatedip" => $response['ip']
            ), array("id" => $params['serviceid']));
        } else {
            throw new Exception($response);
        }
    } catch (Exception $e) {
        // Record the error in WHMCS's module log.
        logModuleCall(
            'ssd100tb',
            __FUNCTION__,
            $params,
            //$e->getTraceAsString()
            $e->getMessage()
        );

        return $e->getMessage();
    }

    return 'success';
}

function ssd100tb_SuspendAccount(array $params)
{
    try {
        $API = new API($params['configoption4']);

        if ($params['status'] === 'Suspended') {
            throw new Exception($params['domain'] . ' is already suspended.');
        } else if ($params['status'] === 'Active') {
            $response = $API->post("/vps.json/servers/{$params['serverid']}/shutdown");
        }
    } catch (Exception $e) {
        // Record the error in WHMCS's module log.
        logModuleCall(
            'ssd100tb',
            __FUNCTION__,
            $params,
            //$e->getTraceAsString()
            $e->getMessage()
        );

        return $e->getMessage();
    }

    return 'success';
}

function ssd100tb_UnsuspendAccount(array $params)
{
    try {
        $API = new API($params['configoption4']);

        if ($params['status'] === 'Suspended') {
            $response = $API->post("/vps.json/servers/{$params['serverid']}/startup");
        } else if ($params['status'] === 'Active') {
            throw new Exception($params['domain'] . ' is already active.');
        }
    } catch (Exception $e) {
        // Record the error in WHMCS's module log.
        logModuleCall(
            'ssd100tb',
            __FUNCTION__,
            $params,
            //$e->getTraceAsString()
            $e->getMessage()
        );

        return $e->getMessage();
    }

    return 'success';
}

function ssd100tb_TerminateAccount (array $params)
{
    try {
        $API = new API($params['configoption4']);

        $response = $API->delete("/vps.json/servers/{$params['serverid']}");

        if ($response != true) {
            return 'Failed to delete SSD VPS server.';
        } else {
            update_query("tblhosting", array(
                "server" => 0,
                "dedicatedip" => ''
            ), array("id" => $params['serviceid']));
        }
    } catch (Exception $e) {
        // Record the error in WHMCS's module log.
        logModuleCall(
            'ssd100tb',
            __FUNCTION__,
            $params,
            //$e->getTraceAsString()
            $e->getMessage()
        );

        return $e->getMessage();
    }

    return 'success';
}

function ssd100tb_reboot(array $params)
{
    try {
        $API = new API($params['configoption4']);

        if ($params['status'] === 'Suspended') {
            throw new Exception($params['domain'] . ' is currently suspended and cannot perform power actions.');
        } else if ($params['status'] === 'Active') {
            $response = $API->post("/vps.json/servers/{$params['serverid']}/reboot");
        }
    } catch (Exception $e) {
        // Record the error in WHMCS's module log.
        logModuleCall(
            'ssd100tb',
            __FUNCTION__,
            $params,
            //$e->getTraceAsString()
            $e->getMessage()
        );

        return $e->getMessage();
    }

    return 'success';
}

function ssd100tb_shutdown(array $params)
{
    try {
        $API = new API($params['configoption4']);

        if ($params['status'] === 'Suspended') {
            throw new Exception($params['domain'] . ' is currently suspended and cannot perform power actions.');
        } else if ($params['status'] === 'Active') {
            $response = $API->post("/vps.json/servers/{$params['serverid']}/shutdown");
        }
    } catch (Exception $e) {
        // Record the error in WHMCS's module log.
        logModuleCall(
            'ssd100tb',
            __FUNCTION__,
            $params,
            //$e->getTraceAsString()
            $e->getMessage()
        );

        return $e->getMessage();
    }

    return 'success';
}

function ssd100tb_startup(array $params)
{
    try {
        $API = new API($params['configoption4']);

        if ($params['status'] === 'Suspended') {
            throw new Exception($params['domain'] . ' is currently suspended and cannot perform power actions.');
        } else if ($params['status'] === 'Active') {
            $response = $API->post("/vps.json/servers/{$params['serverid']}/startup");
        }
    } catch (Exception $e) {
        // Record the error in WHMCS's module log.
        logModuleCall(
            'ssd100tb',
            __FUNCTION__,
            $params,
            //$e->getTraceAsString()
            $e->getMessage()
        );

        return $e->getMessage();
    }

    return 'success';
}

function ssd100tb_AdminCustomButtonArray()
{
    $buttonarray = array(
        "Reboot VPS" => "reboot",
        "Power On VPS" => "startup",
        "Power Off VPS" => "shutdown",
    );

    return $buttonarray;
}

function ssd100tb_ClientAreaCustomButtonArray()
{
    $buttonarray = array(
        "Reboot Server" => "reboot",
        "Power On VPS" => "startup",
        "Power Off VPS" => "shutdown",
    );

    return $buttonarray;
}