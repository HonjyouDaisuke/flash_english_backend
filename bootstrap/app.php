<?php

require_once __DIR__ . '/env.php';

// .env読み込み
loadEnv(__DIR__ . '/../.env');
echo __DIR__ . "\n\n";

// 環境定義
define('APP_ENV', getenv('APP_ENV') ?: 'local');

// パス統一（超重要）
define('BASE_PATH', match (APP_ENV) {
	'production' => '/home/kobe-football/flash_english_backend',
	default => dirname(__DIR__, 1),
});
