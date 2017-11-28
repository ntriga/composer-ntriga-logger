<?php

use Ntriga\Logger;

require __DIR__ . '/vendor/autoload.php';

// init logger
$logger = new Logger();

// log
$resp = $logger->warning(
	'front',
	'title',
	'description'
);

var_dump($resp);
