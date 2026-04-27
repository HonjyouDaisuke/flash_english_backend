<?php
require_once __DIR__ . '/../bootstrap/app.php';
require_once BASE_PATH . '/bootstrap/logger.php';
require_once BASE_PATH . '/vendor/autoload.php';

// ==============================
// 1. PDO接続
// ==============================
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

function runSqlFiles(PDO $pdo, string $dir)
{
	$files = glob($dir . '/*.sql');
	sort($files);

	foreach ($files as $file) {
		echo "Running: " . basename($file) . "\n";
		$sql = file_get_contents($file);
		$pdo->exec($sql);
	}
}

// ==============================
// 3. DB作成
// ==============================
// echo "== Create Database ==\n";
// runSqlFiles($pdo, __DIR__ . '/create_db');

// DB選択（ハイフン対策）
// $pdo->exec("USE `$dbname`");

// ==============================
// 4. Migration
// ==============================
echo "== Run Migrations ==\n";
runSqlFiles($pdo, __DIR__ . '/migrations');

// ==============================
// 5. Seeds
// ==============================
echo "== Run Seeds ==\n";
runSqlFiles($pdo, __DIR__ . '/seeds');

echo "✅ Done!\n";
