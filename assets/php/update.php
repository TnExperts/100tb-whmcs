<?php

if ($_POST) {
	require_once("../classes/tera-api.php");

	$encryptionMethod = "AES-256-CBC";
	$secretHash = "25c6c7ff35b9979b151f2136cd13b0ff";
	$apiKey = openssl_decrypt($_POST['apiKey'], $encryptionMethod, $secretHash);

	$result = new stdClass();
	$result->data = null;
	$result->message = '';

	try {
		$teraApi = new TeraAPI($apiKey);

		$params = array(
			'server' => $_POST['serverId'],
			'ip' => $_POST['ipAddress'],
			'ptr' => $_POST['ipPtr']
		);

		$response = $teraApi->put('/ip.json/{server}/{ip}',$params);

		if ($response) {
			$result->success = true;
		} else {
			$result->success = false;
		}
	} catch (Exception $e) {
		$result->message = $e->getMessage();
		$result->success = false;
	}

	exit(json_encode($result));
}
