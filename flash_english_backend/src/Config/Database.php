<?php

namespace App\Config;

use PDO;

class Database
{
	private PDO $pdo;

	public function __construct()
	{
		$this->pdo = self::connect();
	}

	public static function connect(): PDO
	{
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
		$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

		return $pdo;
	}

	public function beginTransaction(): bool
	{
		return $this->pdo->beginTransaction();
	}

	public function commit(): bool
	{
		return $this->pdo->commit();
	}

	public function rollBack(): bool
	{
		return $this->pdo->rollBack();
	}
}
