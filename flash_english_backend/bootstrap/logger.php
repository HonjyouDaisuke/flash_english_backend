<?php

use Monolog\Logger;
use Monolog\Level;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Formatter\LineFormatter;

require_once BASE_PATH . '/../vendor/autoload.php';

function logger(): Logger
{
	static $logger = null;

	if ($logger !== null) {
		return $logger;
	}

	$env = $_ENV['APP_ENV'] ?? 'local';
	$levelText = $_ENV['LOG_LEVEL'] ?? 'debug';

	$level = match (strtolower($levelText)) {
		'debug' => Level::Debug,
		'info' => Level::Info,
		'notice' => Level::Notice,
		'warning' => Level::Warning,
		'error' => Level::Error,
		'critical' => Level::Critical,
		default => Level::Debug,
	};

	$logDir = BASE_PATH . '/storage/logs';
	if (!is_dir($logDir)) {
		mkdir($logDir, 0775, true);
	}

	$handler = new RotatingFileHandler(
		$logDir . '/app.log',
		14,
		$level
	);

	$formatter = new LineFormatter(
		"[%datetime%] %level_name%: %message% %context%\n",
		"Y-m-d H:i:s",
		true,
		true
	);

	$handler->setFormatter($formatter);

	$logger = new Logger('app');
	$logger->pushHandler($handler);

	return $logger;
}
