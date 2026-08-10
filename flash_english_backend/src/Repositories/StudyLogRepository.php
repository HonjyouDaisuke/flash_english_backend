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
		string $id,
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
			":id" => $id,
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

	public function getSince(string $userId, ?string $since): array
	{
		$sql = file_get_contents(__DIR__ . "/sql/select_study_logs_since.sql");
		$stmt = $this->pdo->prepare($sql);
		if (!$stmt) {
			throw new \RuntimeException("Failed to prepare statement for fetching study logs.");
		}

		if ($since !== null) {
			$since = (new \DateTime($since))
				->format('Y-m-d H:i:s');
		}

		$stmt->execute([
			":user_id" => $userId,
			":since" => $since ?? '2026-04-01 00:00:00',
		]);

		return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}
}
