<?php

declare(strict_types=1);

/**
 * deploy-check.php
 * 配置先:
 * /home/kobe-football/www/flash_english_backend_test/deploy-check.php
 *
 * ブラウザ確認:
 * https://あなたのドメイン/flash_english_backend_test/deploy-check.php
 */

header('Content-Type: text/plain; charset=utf-8');

echo "=== FlashEnglish Backend Deploy Check ===\n";
echo "Time: " . date('Y-m-d H:i:s') . "\n\n";

/**
 * public 側:
 * /home/kobe-football/www/flash_english_backend_test
 *
 * private 側:
 * /home/kobe-football/flash_english_backend_test
 */
$privateRoot = dirname(__DIR__, 2);
echo dirname(__DIR__, 2) . "\n";
echo "[1] Private Path Check\n";
echo $privateRoot . "\n";
echo is_dir($privateRoot) ? "OK\n\n" : "NG\n\n";

/**
 * オートロード確認
 */
$autoload = $privateRoot . '/vendor/autoload.php';

echo "[2] Composer Autoload Check\n";
echo $autoload . "\n";

if (!file_exists($autoload)) {
	echo "NG: autoload.php not found\n";
	exit;
}

require_once $autoload;
echo "OK\n\n";

/**
 * src ファイル確認
 */
$serviceFile = $privateRoot . '/src/Services/JwtService.php';

echo "[3] Backend Source Check\n";
echo $serviceFile . "\n";
echo file_exists($serviceFile) ? "OK\n\n" : "NG\n\n";

/**
 * クラスロード確認
 */
echo "[4] Class Load Check\n";

if (class_exists(\App\Services\JwtService::class)) {
	echo "OK: JwtService loaded\n\n";
} else {
	echo "NG: JwtService not loaded\n";
	exit;
}

/**
 * 実行確認
 */
echo "[5] Method Execute Check\n";

try {
	$token = \App\Services\JwtService::generateAccessToken('test-user');
	echo "OK: Token generated\n";
	echo substr($token, 0, 30) . "...\n\n";
} catch (Throwable $e) {
	echo "NG: " . $e->getMessage() . "\n";
	exit;
}

echo "=== DEPLOY SUCCESS ===\n";
