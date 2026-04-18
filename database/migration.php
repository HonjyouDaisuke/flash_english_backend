<?php

// ==============================
// 1. env 読み込み
// ==============================
$env = require __DIR__ . '/../src/Config/env.local.php';

$host = $env['db_host'];
$port = $env['db_port'];
$dbname = $env['db_name'];
$user = $env['db_user'];
$password = $env['db_pass'];

// ==============================
// 2. PDO接続（DBなし）
// ==============================
$pdo = new PDO(
	"mysql:host=$host;port=$port;charset=utf8mb4",
	$user,
	$password,
	[PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
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
echo "== Create Database ==\n";
runSqlFiles($pdo, __DIR__ . '/create_db');

// DB選択（ハイフン対策）
$pdo->exec("USE `$dbname`");

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
