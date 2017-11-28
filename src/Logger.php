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
		$this->domain = (string) $domain;
		if (empty($this->domain)) {
			$this->domain = isset($_SERVER['HTTP_HOST']) ? (string) $_SERVER['HTTP_HOST'] : '';
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

	public function alert($category, $title, $description = null)
	{
		return $this->log($category, 'alert', $title, $description);
	}
	public function critical($category, $title, $description = null)
	{
		return $this->log($category, 'critical', $title, $description);
	}
	public function debug($category, $title, $description = null)
	{
		return $this->log($category, 'debug', $title, $description);
	}
	public function emergency($category, $title, $description = null)
	{
		return $this->log($category, 'emergency', $title, $description);
	}
	public function error($category, $title, $description = null)
	{
		return $this->log($category, 'error', $title, $description);
	}
	public function info($category, $title, $description = null)
	{
		return $this->log($category, 'info', $title, $description);
	}
	public function notice($category, $title, $description = null)
	{
		return $this->log($category, 'notice', $title, $description);
	}
	public function warning($category, $title, $description = null)
	{
		return $this->log($category, 'warning', $title, $description);
	}
}
