<?php

require_once __DIR__ . '/env.php';

// .env読み込み
loadEnv(__DIR__ . '/../.env');

// 環境定義
define('APP_ENV', getenv('APP_ENV') ?: 'local');

// パス統一（超重要）
define('BASE_PATH', match (APP_ENV) {
	'server' => '/home/kobe-football/flash_english_backend',
	default => dirname(__DIR__, 1),
});

/**
 * DB config
 */
if (APP_ENV === 'server') {

	// .env / GitHub Secrets / Actions 用
	define('DB_HOST', getenv('DB_HOST'));
	define('DB_PORT', '3306');
	define('DB_NAME', getenv('DB_NAME'));
	define('DB_USER', getenv('DB_USER'));
	define('DB_PASS', getenv('DB_PASS'));
} else {

	// PHPファイル設定
	$config = require BASE_PATH . '/src/config/env.local.php';

	define('DB_HOST', $config['db_host']);
	define('DB_PORT', $config['db_port']);
	define('DB_NAME', $config['db_name']);
	define('DB_USER', $config['db_user']);
	define('DB_PASS', $config['db_pass']);
}
