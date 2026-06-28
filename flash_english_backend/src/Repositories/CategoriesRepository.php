<?php

namespace App\Repositories;

use PDO;

class CategoriesRepository
{
	private PDO $pdo;

	public function __construct(PDO $pdo)
	{
		$this->pdo = $pdo;
	}

	public function getAll(): array
	{
		logger()->debug("getAllCategories...");

		$sql = file_get_contents(__DIR__ . "/sql/select_all_categories.sql");

		$stmt = $this->pdo->query($sql);

		if ($stmt === false) {
			throw new \RuntimeException("Failed to fetch categories.");
		}

		return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}
}
