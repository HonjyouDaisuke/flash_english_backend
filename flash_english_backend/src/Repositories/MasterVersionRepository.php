<?php

namespace App\Repositories;

use PDO;

class MasterVersionRepository
{
	private PDO $pdo;

	public function __construct(PDO $pdo)
	{
		$this->pdo = $pdo;
	}

	public function get(string $versionName): ?string
	{
		$sql = file_get_contents(__DIR__ . "/sql/select_master_version.sql");
		$stmt = $this->pdo->prepare($sql);
		if (!$stmt) {
			return null;
		}

		$result = $stmt->execute([":version_name" => $versionName]);

		if (!$result) {
			return null;
		}
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		if ($row) {
			return $row['version_no'];
		}
		return null;
	}

	public function getVersionInfo(string $versionName): ?array
	{
		$sql = file_get_contents(__DIR__ . "/sql/select_master_version.sql");
		$stmt = $this->pdo->prepare($sql);
		if (!$stmt) {
			return null;
		}

		$result = $stmt->execute([":version_name" => $versionName]);

		if (!$result) {
			return null;
		}
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		if ($row) {
			return $row;
		}
		return null;
	}
}
