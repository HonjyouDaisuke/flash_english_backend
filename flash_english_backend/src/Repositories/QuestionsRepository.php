<?php

namespace App\Repositories;

use PDO;

class QuestionsRepository
{
	private PDO $pdo;

	public function __construct(PDO $pdo)
	{
		$this->pdo = $pdo;
	}

	public function getAll(): array
	{
		$sql = file_get_contents(__DIR__ . "/sql/select_all_questions.sql");

		$stmt = $this->pdo->query($sql);
		if ($stmt === false) {
			throw new \RuntimeException("Failed to fetch questions.");
		}

		return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}
}
