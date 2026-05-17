<?php

namespace App\Repositories;

use PDO;

class StudyLogRepository
{
	private PDO $pdo;

	public function __construct(PDO $pdo)
	{
		$this->pdo = $pdo;
	}

	public function save(
		string $userId,
		int $categoryNo,
		int $unitNo,
		int $questionNo,
		bool $isCorrect,
		int $sessionId,
		int $durationSeconds,
		?string $createdAt = null,
	): bool {
		$sql = file_get_contents(__DIR__ . "/sql/insert_study_logs.sql");
		$stmt = $this->pdo->prepare($sql);
		if (!$stmt) {
			return false;
		}

		if ($createdAt !== null) {
			$createdAt = (new \DateTime($createdAt))
				->format('Y-m-d H:i:s');
		}

		$result = $stmt->execute([
			":user_id" => $userId,
			":category_no" => $categoryNo,
			":unit_no" => $unitNo,
			":question_no" => $questionNo,
			":is_correct" => $isCorrect ? 1 : 0,
			":session_id" => $sessionId,
			":duration_seconds" => $durationSeconds,
			":created_at" => $createdAt ?? date("Y-m-d H:i:s"),
		]);

		return $result;
	}
}
