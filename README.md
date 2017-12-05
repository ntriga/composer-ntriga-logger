# README #

## composer.json ##

### Edit composer.json ###

```json
{
	"repositories": [
		{
			"type": "git",
			"url": "https://dspventures@bitbucket.org/dspdevteam/composer-ntriga-logger.git"
		}
	]
}
```

### Require package ###

```
composer require ntriga/logger:dev-master
```

## PHP ##

### Add logging to local logs ###

```php
use Ntriga\Logger;

require __DIR__ . '/../vendor/autoload.php';

$logger = new Logger();

$resp = $logger->warning(
	'front',
	'titel',
	'omschrijving',
	[
		'param1' => 'value1',
		'param2' => 'value2',
		'param3' => 'value3',
	]
);

var_dump($resp);
```


### Synch local logs to datajane.be ###

```php
use Ntriga\Logger;

require __DIR__ . '/../vendor/autoload.php';

$logger = new Logger();

$resp = $logger->synch();

var_dump($resp);
```
