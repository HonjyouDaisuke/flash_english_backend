<?php

declare(strict_types=1);

// ini_set('display_errors', '1');
// ini_set('display_startup_errors', '1');
// error_reporting(E_ALL);

echo "BOOT START\n";

require_once __DIR__ . '/../../flash_english_backend/bootstrap/app.php';

echo "Base path = " . BASE_PATH . "\n";

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
	echo "OK\n\n";
} else {
	echo "NG\n";
	exit;
}

/**
 * Token Test
 */
echo "[3] Token Test\n";

try {
	$token = \App\Services\JwtService::generateAccessToken('test-user');
	echo "OK: " . substr($token, 0, 30) . "...\n\n";
} catch (Throwable $e) {
	echo "NG: " . $e->getMessage() . "\n";
	exit;
}

/**
 * DB Connection Test
 */
echo "[4] Database Connection Test\n";
// echo "db_host:" . DB_HOST . "\n";
// echo "db_host:" . DB_PORT . "\n";
// echo "db_host:" . DB_NAME . "\n";
// echo "db_host:" . DB_USER . "\n";
// echo "db_host:" . DB_PASS . "\n";

try {
	$dsn = sprintf(
		'mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4',
		DB_HOST,
		DB_PORT,
		DB_NAME
	);

	$pdo = new PDO(
		$dsn,
		DB_USER,
		DB_PASS,
		[
			PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
			PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
		]
	);

	echo "Connection OK\n";

	$stmt = $pdo->query('SELECT NOW() AS now_time');
	$row = $stmt->fetch();

	echo "DB Time: " . $row['now_time'] . "\n\n";
} catch (Throwable $e) {
	echo "NG\n";
	error_log('[deploy_check] db error: ' . $e->getMessage());
	exit;
}

echo "=== DONE ===\n";
