<?php
/**
 * Implementation class for Tera's REST API Server
 * @author Robbie Hernandez <support@100tb.com>
 * @date 2015-09-03
 * @version 0.10.2
 *
 * Example usages for this class:
 * ---------
 * https://cp.100tb.com/rest-api/docs/
 * ---------
 *
 * 200 OK: Success
 * 400 Bad Request: The request is invalid.
 * 401 Unauthorized: Authentication credentials are missing or incorrect
 * 404 Not Found: The resource requested, such as a user, does not exist.
 * 500 Internal Server Error: Something is broke. Please contact an administrator.
 *
 */
class TeraAPI {

	protected $_apiUserAgent = "TERA_API_1_JSON/PHP";
	//protected $_apiUrl = 'https://cp.100tb.com/rest-api/';
	protected $_apiUrl = 'http://localhost/tera/rest-api/';

	private $_apiKey = '';
	private $_params = array();

	public function __construct($key) {
		$this->_apiKey = $key;
	}

	private function execute($route, $request) {

		$url = $this->buildUrl($route, $request);

		$handle = curl_init();

		curl_setopt($handle, CURLOPT_URL, $url);
		curl_setopt($handle, CURLOPT_HEADER, 0);
		curl_setopt($handle, CURLOPT_USERAGENT, $this->_apiUserAgent);
		curl_setopt($handle, CURLOPT_TIMEOUT, 60);

		if (substr($url,0,5) != 'https') {
			curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, 1);
			curl_setopt($handle, CURLOPT_SSL_VERIFYHOST, 2);
		} else {
			curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, 0);
			curl_setopt($handle, CURLOPT_SSL_VERIFYHOST, 0);
		}

		if ($request !== 'GET') {
			switch ($request) {
				case "POST":
					curl_setopt($handle, CURLOPT_POST, TRUE);
					curl_setopt($handle, CURLOPT_POSTFIELDS, http_build_query($this->_params));
					break;
				case "PUT":
					curl_setopt($handle, CURLOPT_CUSTOMREQUEST, 'PUT');
					curl_setopt($handle, CURLOPT_POSTFIELDS, http_build_query($this->_params));
					break;
				case "DELETE":
					curl_setopt($handle, CURLOPT_CUSTOMREQUEST, 'DELETE');
					curl_setopt($handle, CURLOPT_POSTFIELDS, http_build_query($this->_params));
					break;
			}
		}

		curl_setopt($handle, CURLOPT_RETURNTRANSFER, TRUE);

		$response = curl_exec($handle);
		if (isset($response[0])) {
			if ($response[0] == '[' || $response[0] == '{') {
				$response = json_decode($response, TRUE);
			}
		}

		$result = array(
			'data' => $response,
			'info' => curl_getinfo($handle),
		);

		curl_close($handle);

		if ($result['info']['http_code'] !== 200) {
			throw new Exception($result['data']['error']['message'],$result['info']['http_code']);
		}

		return $result['data'];
	}

	private function buildUrl($route,$request) {

		$route = array_values(array_filter(explode('/',$route)));

		for($i = 0; $i < count($route); $i++) {
			foreach ($this->_params as $key => $param) {
				if ('{'.$key.'}' == $route[$i]) {
					$route[$i] = $param;
					unset($this->_params[$key]);
				}
			}
		}

		$url = $this->_apiUrl.implode('/',$route).'?api_key=' . $this->_apiKey;

		if ($request == 'GET') {
			$url = $url.'&'.http_build_query($this->_params);
		}

		return preg_replace('/\s+/', '', $url);
	}

	public function get($route, $params = array()){
		$this->_params = $params;
		return $this->execute($route, 'GET');
	}

	public function post($route, $params = array()){
		$this->_params = $params;
		return $this->execute($route, 'POST');
	}

	public function put($route, $params = array()){
		$this->_params = $params;
		return $this->execute($route, 'PUT');
	}

	public function delete($route, $params = array()){
		$this->_params = $params;
		return $this->execute($route, 'DELETE');
	}
}