<?php

namespace App\Repositories;

use PDO;

class UserSettingsRepository
{
	private PDO $pdo;

	public function __construct(PDO $pdo)
	{
		$this->pdo = $pdo;
	}
	public function save(string $userId, string $settingKey, string $value,): bool
	{
		$sql = file_get_contents(__DIR__ . "/sql/insert_user_settings.sql");
		logger()->debug("Saving user setting userId=" . $userId . " key=" . $settingKey);
		$stmt = $this->pdo->prepare($sql);
		if (!$stmt) {
			return false;
		}
		$result = $stmt->execute([
			":user_id" => $userId,
			":setting_key" => $settingKey,
			":value" => $value,
		]);
		return $result;
	}
}
