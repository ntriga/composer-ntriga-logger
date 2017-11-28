<?php
namespace Ntriga;

use GuzzleHttp\Client;

class Logger
{
	private $domain;
	private $endpoint = 'http://datajanebe.dev/api/log';
	private $guzzle;

	public function __construct($domain = null)
	{
		// reset domain ?
		if ($domain === null) {
			$domain = isset($_SERVER['HTTP_HOST']) ? (string) $_SERVER['HTTP_HOST'] : '';
		}

		// init guzzle
		$this->guzzle = new Client();
	}

	public function log($category, $type, $title, $description = null)
	{
		// post
		$resp = $this->guzzle->request(
			'POST',
			$this->endpoint . '/add',
			[
				'form_params' => [
					'domain' => $this->domain,
					'category' => (string) $category,
					'type' => (string) $type,
					'title' => (string) $title,
					'description' => (string) $description,
				]
			]
		);

		// set return var
		$ret = ['success' => false, 'data' => []];

		// reset return var ?
		if ( $resp->getStatusCode() == 200 ) {
			$ret['success'] = true;
			$ret['data'] = @json_decode($resp->getBody()->getContents(), true);
		}

		// return
		return $ret;
	}

	public function addAlert($category, $title, $description = null)
	{
		return $this->log($category, 'alert', $title, $description);
	}
	public function addCritical($category, $title, $description = null)
	{
		return $this->log($category, 'critical', $title, $description);
	}
	public function addDebug($category, $title, $description = null)
	{
		return $this->log($category, 'debug', $title, $description);
	}
	public function addEmergency($category, $title, $description = null)
	{
		return $this->log($category, 'emergency', $title, $description);
	}
	public function addError($category, $title, $description = null)
	{
		return $this->log($category, 'error', $title, $description);
	}
	public function addInfo($category, $title, $description = null)
	{
		return $this->log($category, 'info', $title, $description);
	}
	public function addNotice($category, $title, $description = null)
	{
		return $this->log($category, 'notice', $title, $description);
	}
	public function addWarning($category, $title, $description = null)
	{
		return $this->log($category, 'warning', $title, $description);
	}
}
