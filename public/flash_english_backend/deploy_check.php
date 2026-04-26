<?php

declare(strict_types=1);
require_once __DIR__ . '/../../bootstrap/app.php';
require_once BASE_PATH . '/bootstrap/logger.php';
require_once BASE_PATH . '/vendor/autoload.php';


header('Content-Type: text/plain; charset=utf-8');

echo "=== FlashEnglish Backend Deploy Check ===\n";
echo "Time: " . date('Y-m-d H:i:s') . "\n\n";

echo "[ENV]\n";
echo "APP_ENV: " . APP_ENV . "\n";
echo "BASE_PATH: " . BASE_PATH . "\n\n";

/**
 * Composer
 */
$autoload = BASE_PATH . '/vendor/autoload.php';

echo "[1] Composer Autoload Check\n";
echo $autoload . "\n";

if (!file_exists($autoload)) {
	echo "NG\n";
	exit;
}

require_once $autoload;
echo "OK\n\n";

/**
 * JWT
 */
echo "[2] JWT Class Check\n";

if (class_exists(\App\Services\JwtService::class)) {
	echo "OK\n";
} else {
	echo "NG\n";
	exit;
}

/**
 * Test
 */
echo "[3] Token Test\n";

try {
	$token = \App\Services\JwtService::generateAccessToken('test-user');
	echo "OK: " . substr($token, 0, 30) . "...\n";
} catch (Throwable $e) {
	echo "NG: " . $e->getMessage() . "\n";
}

echo "\n=== DONE ===\n";
