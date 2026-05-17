<?php

namespace App\Application\UseCases;

use PDO;
use App\Config\Database;
use App\Repositories\StudyLogRepository;
use App\Repositories\UnitHighScoreRepository;
use Throwable;

class SyncUseCase
{
	public function __construct(
		private StudyLogRepository $studyLogRepository,
		private UnitHighScoreRepository $unitScoreRepository,
		private PDO $db,
	) {}

	public function execute(
		string $userId,
		array $events,
	): array {

		$processedIds = [];

		$this->db->beginTransaction();

		try {
			foreach ($events as $event) {
				$this->validateUser(
					$userId,
					$event
				);
				$payload = $event["payload"];
				switch ($event["type"]) {
					case "study_log":
						logger()->debug('Processing study_log event', ['event' => $event]);
						$this->studyLogRepository->save(
							$userId,
							$payload["category_no"],
							$payload["unit_no"],
							$payload["question_no"],
							$payload["is_correct"],
							$payload["session_id"],
							$payload["duration_seconds"],
							$payload["created_at"] ?? null,
						);
						break;

					case "unit_score":
						$this->unitScoreRepository->save(
							$userId,
							$payload["category_no"],
							$payload["unit_no"],
							$payload["high_score"],
							$payload["achieved_at"],
						);
						break;
					default:

						throw new \Exception(
							"Unknown event type"
						);
				}

				$processedIds[] =
					$event["event_id"];
			}

			$this->db->commit();

			return $processedIds;
		} catch (Throwable $e) {

			$this->db->rollBack();

			throw $e;
		}
	}


	private function validateUser(
		string $jwtUserId,
		array $event
	): void {

		if (
			$event["user_id"] !== $jwtUserId
		) {
			throw new \Exception(
				"Invalid user"
			);
		}
	}
}
