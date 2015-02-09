<?php

require_once("assets/classes/tera-api.php");

function servers100tb_ConfigOptions()
{
	if(!mysql_num_rows(mysql_query("SHOW TABLES LIKE 'mod_100tb'"))) {
		$query = '
			CREATE TABLE IF NOT EXISTS `mod_100tb` (
				 `serviceid` int(11) unsigned NOT NULL,
				 `serverid` int(11) unsigned NOT NULL,
				 `alternateApiKey` VARCHAR( 255 ) DEFAULT NULL,
				 PRIMARY KEY (`serviceid`),
				 UNIQUE KEY `serverid` (`serverid`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8';

		mysql_query($query);
	}

	$configarray = array(
		"API Key" => array( "Type" => "text", "Size" => "80", "Description" => "assign tera account with key" ),
		'Allow PTR' => array( "Type" => "yesno", "Description" => "Allow clients to set their own PTR records for IP addresses" ),
	);

	return $configarray;
}

function servers100tb_SuspendAccount($params)
{
	$result = select_query("mod_100tb","",array("serviceid"=>$params['serviceid']));

	if ((!$result || mysql_num_rows($result) == '0')) {
		return 'Please assign a Tera server first.';
	}

	$data = mysql_fetch_array($result);

	$serverId = $data['serverid'];

	$apiKey = (!empty($data['alternateApiKey']))?$data['alternateApiKey']:$params["configoption1"];

	$teraApi = new TeraAPI($apiKey);

	$params = array(
		'server' => $serverId
	);

	try {
		$response = $teraApi->post('/servers.json/{server}/disable',$params);
		$success = true;
	} catch(Exception $e) {
		$response = $e->getMessage();
		$success = false;
	}

	if ($success) {
		$result = "success";
	} else {
		$result = "$response";
	}

	return $result;
}


function servers100tb_UnsuspendAccount($params)
{
	$result = select_query("mod_100tb","",array("serviceid"=>$params['serviceid']));

	if ((!$result || mysql_num_rows($result) == '0')) {
		return 'Please assign a Tera server first.';
	}

	$data = mysql_fetch_array($result);

	$serverId = $data['serverid'];

	$apiKey = (!empty($data['alternateApiKey']))?$data['alternateApiKey']:$params["configoption1"];

	$teraApi = new TeraAPI($apiKey);

	$params = array(
		'server' => $serverId
	);

	try {
		$response = $teraApi->post('/servers.json/{server}/enable',$params);
		$success = true;
	} catch(Exception $e) {
		$response = $e->getMessage();
		$success = false;
	}

	if ($success) {
		$result = "success";
	} else {
		$result = "$response";
	}

	return $result;
}


function servers100tb_reboot($params)
{
	$result = select_query("mod_100tb","",array("serviceid"=>$params['serviceid']));

	if ((!$result || mysql_num_rows($result) == '0')) {
		return 'Please assign a Tera server first.';
	}

	$data = mysql_fetch_array($result);

	$serverId = $data['serverid'];

	$apiKey = (!empty($data['alternateApiKey']))?$data['alternateApiKey']:$params["configoption1"];

	$teraApi = new TeraAPI($apiKey);

	$params = array(
		'server' => $serverId
	);

	try {
		$response = $teraApi->post('/servers.json/{server}/reboot',$params);
		$success = true;
	} catch(Exception $e) {
		$response = $e->getMessage();
		$success = false;
	}

	if ($success) {
		$result = "success";
	} else {
		$result = "$response";
	}

	return $result;
}

function servers100tb_ips($params)
{
	$encryptionMethod = "AES-256-CBC";
	$secretHash = "25c6c7ff35b9979b151f2136cd13b0ff";

	$ips = null;
	$error = null;

	$serverId = null;
	$apiKey = null;

	$ptrAllowed = $params["configoption2"];

	$result = select_query("mod_100tb","",array("serviceid"=>$params['serviceid']));

	if ((!$result || mysql_num_rows($result) == '0')) {
		$error = 'Please assign a Tera server first.';
	} else {

		$data = mysql_fetch_array($result);

		$serverId = $data['serverid'];

		$apiKey = (!empty($data['alternateApiKey'])) ? $data['alternateApiKey'] : $params["configoption1"];

		$teraApi = new TeraAPI($apiKey);

		$params = array(
			'server' => $serverId
		);

		try {
			$ips = $teraApi->get('/ip.json/{server}', $params);
		} catch (Exception $e) {
			$ips = null;
			$error = $e->getMessage();
		}

		if ($ips && !empty($ips)) {
			$ipsPretty = json_encode($ips, JSON_PRETTY_PRINT);
			update_query("tblhosting", array(
				"assignedips" => $ipsPretty,
			), array("id" => $params['serviceid']));
		} else {
			$error = 'No additional IP\'s to list';
		}
	}

	if (!$error) {
		return array(
			'templatefile' => 'assets/template/ips',
			'vars' => array(
				'ips' => $ips,
				'ptrAllowed' => $ptrAllowed,
				'serverId' => $serverId,
				'encodedKey' => openssl_encrypt($apiKey, $encryptionMethod, $secretHash)
			),
		);
	} else {
		return $error;
	}
}

/*
function servers100tb_ClientArea($params) {

	$code = '<blockquote>TODO: Get Server Status / power status</blockquote>';

	return $code;
}
*/

function servers100tb_bandwidth($params)
{
	$bandwidth = null;
	$error = null;

	$result = select_query("mod_100tb","",array("serviceid"=>$params['serviceid']));

	if ((!$result || mysql_num_rows($result) == '0')) {
		$error = 'Please assign a Tera server first.';
	} else {

		$data = mysql_fetch_array($result);

		$serverId = $data['serverid'];

		$apiKey = (!empty($data['alternateApiKey'])) ? $data['alternateApiKey'] : $params["configoption1"];

		$teraApi = new TeraAPI($apiKey);

		$params = array(
			'server' => $serverId
		);

		try {
			$response = $teraApi->get('/servers.json/{server}/bandwidth',$params);
			if (isset($response['data']) && !empty($response['data'])) {

				$bandwidth = $response['data'];

				function date_compare($a, $b) {
					$t1 = strtotime($a['date']);
					$t2 = strtotime($b['date']);
					return $t2 - $t1;
				}

				usort($bandwidth, 'date_compare');

				foreach ($bandwidth as $k => $v) {
					$bandwidth[$k]['date'] = date('l jS F Y', strtotime( $v['date']));
				}
			}
		} catch (Exception $e) {
			$error = $e->getMessage();
		}
	}

	if (!$error && $bandwidth) {
		return array(
			'templatefile' => 'assets/template/bandwidth',
			'vars' => array(
				'bandwidth' => $bandwidth
			),
		);
	} else {
		return $error;
	}
}

function servers100tb_AdminCustomButtonArray()
{
	$buttonarray = array(
		"Reboot Server" => "reboot"
	);

	return $buttonarray;
}

function servers100tb_ClientAreaCustomButtonArray()
{
	$buttonarray = array(
		"Reboot Server" => "reboot",
		"Additional IP's" => "ips",
		"Bandwidth Usage" => "bandwidth"
	);

	return $buttonarray;
}


function servers100tb_AdminServicesTabFields($params)
{
	$result = select_query("mod_100tb","",array("serviceid"=>$params['serviceid']));

	if ((!$result || mysql_num_rows($result) == '0')) {
		$serverId = 0;
		$alternateApiKey = '';
		insert_query("mod_100tb",array("serviceid" => $params['serviceid'],"serverid" => $serverId, "alternateApiKey" => $alternateApiKey));
	} else {
		$data = mysql_fetch_array($result);
		$serverId = $data['serverid'];
		$alternateApiKey = $data['alternateApiKey'];
	}

	$apiKey = (!empty($alternateApiKey))?$alternateApiKey:$params["configoption1"];
	$teraApi = new TeraAPI($apiKey);
	$options = null;

	try {
		$response = $teraApi->get('/servers.json');
		array_multisort($response, SORT_DESC);
		foreach($response as $k => $v) {
			if ($serverId == $v['id']) {
				$options .= '<option value="' . $v['id'] . '" selected>' . $v['id'] . ' - ' . $v['fqdn'] . '</option>';
			} else {
				$options .= '<option value="' . $v['id'] . '">' . $v['id'] . ' - ' . $v['fqdn'] . '</option>';
			}
		}
	} catch(Exception $e) {
		//
	}

	$fieldsarray = array(
		'Tera Server List' => '<select name="modulefields[0]">'.$options.'</select>',
		'Api Key' => $params["configoption1"],
		'Alternate API Key' => '<input type="text" name="modulefields[1]" size="70" value="'.$alternateApiKey.'" />',
	);

	return $fieldsarray;
}

function servers100tb_AdminServicesTabFieldsSave($params)
{
	$serviceId = $params['serviceid'];
	$serverId = $_POST['modulefields'][0];
	$alternateApiKey = $_POST['modulefields'][1];

	update_query("mod_100tb",array(
		"serviceid" => $serviceId,
		"serverid" => $serverId,
		"alternateApiKey" => $alternateApiKey
	));

	$apiKey = (!empty($alternateApiKey))?$alternateApiKey:$params["configoption1"];

	$teraApi = new TeraAPI($apiKey);

	$params = array(
		'server' => $serverId
	);

	try {
		$response = $teraApi->get('/servers.json/{server}',$params);
	} catch(Exception $e) {
		$response = null;
	}

	if ($response) {

		$domain = (isset($response['fqdn']))? $response['fqdn']:'';
		$userName = (isset($response['info']['login_details']['username']))? $response['info']['login_details']['username']:'';
		$dedicatedIp = (isset($response['public_ip']))? $response['public_ip']:'';
		$assignedips = (isset($response['ips']))? json_encode($response['ips'], JSON_PRETTY_PRINT):'';
		$bandwidthLimit = (isset($response['info']['bandwidth']['limit']))? $response['info']['bandwidth']['limit']:0;

		update_query("tblhosting", array(
			"domain" => $domain,
			"username" => $userName,
			"dedicatedip" => $dedicatedIp,
			"assignedips" => $assignedips,
			"bwlimit" => $bandwidthLimit
		), array("id" => $serviceId));
	}
}