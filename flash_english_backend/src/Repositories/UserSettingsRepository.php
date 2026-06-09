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
		if ($sql === false) {
			logger()->error("Failed to load SQL: insert_user_settings.sql");
			return false;
		}
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

	public function findByKey(string $userId, string $settingKey,): ?array
	{
		$sql = file_get_contents(__DIR__ . "/sql/select_setting_by_key.sql");
		$stmt = $this->pdo->prepare($sql);
		$stmt->execute([
			':user_id' => $userId,
			':setting_key' => $settingKey,
		]);

		$row = $stmt->fetch(PDO::FETCH_ASSOC);

		return $row ?: null;
	}

	public function findAll(string $userId,): array
	{
		$sql = file_get_contents(__DIR__ . "/sql/select_all_settings.sql");
		$stmt = $this->pdo->prepare($sql);

		$stmt->execute([
			':user_id' => $userId,
		]);

		return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}
}
