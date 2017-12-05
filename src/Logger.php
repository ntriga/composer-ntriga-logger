<?php

namespace Ntriga;

use GuzzleHttp\Client;
use Monolog\Logger as MonologLogger;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\StreamHandler;
use Symfony\Component\Finder\Finder;

class Logger
{
	private $domain;

	public function __construct($domain = null)
	{
		// reset domain ?
		$this->domain = (string) $domain;
		if (empty($this->domain)) {
			$this->domain = isset($_SERVER['HTTP_HOST']) ? (string) $_SERVER['HTTP_HOST'] : '';
		}
	}

	private function getLogFiles()
	{
		$items = array();
		$finder = new Finder();

		foreach ($finder->in($this->getLogPath())->files()->name('*.log') as $file) {
			$items[] = array(
				'path' => $file->getRealPath(),
				'delete' => ( filemtime($file->getRealPath()) < strtotime('today 00:00')),
			);
		}

		return $items;
	}

	private function getLogPath()
	{
		return __DIR__ . '/../logs/';
	}

	public function log($category, $type, $title, $description = null, array $extra = array())
	{
		// redefine
		$category = (string) $category;
		$type = (string) $type;
		$title = (string) $title;
		$description = (string) $description;

		// validate $type
		switch ($type) {
			case 'alert';
			case 'critical';
			case 'emergency':
			case 'error':
			case 'info':
			case 'notice':
			case 'warning':
				break;

			default:
				$type = 'debug';
				break;
		}

		// set current timezone
		$timezone = date_default_timezone_get();

		// overwrite it to "our" timezone
		date_default_timezone_set('Europe/Brussels');

		// init monolog
		$monolog = new MonologLogger($this->domain);

		// log file
		$dateFormat = 'Y-m-d H:i:s';
		$output = "[%datetime%] %context%\n";
		$formatter = new LineFormatter($output, $dateFormat);

		$logfile = $this->getLogPath() .
					$type . '/' .
					date('Ymd') . '.log'
					;

		// create handler
		$stream = new StreamHandler($logfile);

		// set formatter
		$stream->setFormatter($formatter);

		// add handler
		$monolog->pushHandler($stream);

		// set params
		$params = array(
			'title' => $title,
			'category' => $category,
			'description' => $description,
			'extra' => $extra,
		);

		// log
		$monolog->$type(
			null, $params
		);

		// reset timezone
		date_default_timezone_set($timezone);

		// return
		return array(
			'success' => true,
			'logfile' => $logfile,
			'data' => $params,
		);
	}

	public function synch()
	{
		// get files to synch
		$files = $this->getLogFiles();

		// validate
		if (empty($files)) return;

		// set endpoint
		$endpoint = (
			isset($_SERVER['HTTP_HOST']) &&
			preg_match('/\.dev$/', $_SERVER['HTTP_HOST'])
		) ? 'http://datajanebe.dev/api/log' : 'http://logger.datajane.be/api/log';

		// init guzzle
		$guzzle = new Client();

		// iterate through files and post them one by one
		// we cannot give GET params per file, so we need to make a seperate call for each file
		foreach ($files as &$file) {
			// get type, filename
			$_file = explode('/', $file['path']);
			$filename = array_pop($_file);
			$type = array_pop($_file);

			$resp = $guzzle->request(
				'POST',
				$endpoint . '/synch',
				array(
					'query' => array(
						'domain' => $this->domain,
						'type' => $type,
					),
					'multipart' => array(
						array(
							'name' => 'logfile',
							'filename' => $filename,
							'contents' => fopen($file['path'], 'r'),
						)
					),
				)
			);

			$file['processed'] = ($resp->getStatusCode() == 200);

			// delete file ?
			if ($file['processed'] && (bool) $file['delete']) {
				@unlink($file['path']);
			}
		}

		// return
		return $files;
	}

	public function alert($category, $title, $description = null, array $extra = array())
	{
		return $this->log($category, 'alert', $title, $description, $extra);
	}
	public function critical($category, $title, $description = null, array $extra = array())
	{
		return $this->log($category, 'critical', $title, $description, $extra);
	}
	public function debug($category, $title, $description = null, array $extra = array())
	{
		return $this->log($category, 'debug', $title, $description, $extra);
	}
	public function emergency($category, $title, $description = null, array $extra = array())
	{
		return $this->log($category, 'emergency', $title, $description, $extra);
	}
	public function error($category, $title, $description = null, array $extra = array())
	{
		return $this->log($category, 'error', $title, $description, $extra);
	}
	public function info($category, $title, $description = null, array $extra = array())
	{
		return $this->log($category, 'info', $title, $description, $extra);
	}
	public function notice($category, $title, $description = null, array $extra = array())
	{
		return $this->log($category, 'notice', $title, $description, $extra);
	}
	public function warning($category, $title, $description = null, array $extra = array())
	{
		return $this->log($category, 'warning', $title, $description, $extra);
	}
}
