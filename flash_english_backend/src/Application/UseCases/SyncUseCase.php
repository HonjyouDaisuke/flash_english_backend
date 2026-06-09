<?php

namespace App\Application\UseCases;

use PDO;
use App\Config\Database;
use App\Repositories\StudyLogRepository;
use App\Repositories\UnitHighScoreRepository;
use App\Repositories\UserSettingsRepository;
use Throwable;

class SyncUseCase
{
	public function __construct(
		private StudyLogRepository $studyLogRepository,
		private UnitHighScoreRepository $unitScoreRepository,
		private UserSettingsRepository $userSettingsRepository,
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
						logger()->debug('Processing study_log event: ' . $event["event_id"]);
						$ok = $this->studyLogRepository->save(
							$userId,
							$payload["category_no"],
							$payload["unit_no"],
							$payload["question_no"],
							$payload["is_correct"],
							$payload["session_id"],
							$payload["duration_seconds"],
							$payload["created_at"] ?? null,
						);
						if (!$ok) {
							throw new \Exception("Failed to save study log");
						}
						break;

					case "unit_score":
						logger()->debug('Processing unit_score event: ' . $event["event_id"]);
						$ok = $this->unitScoreRepository->save(
							$userId,
							$payload["category_no"],
							$payload["unit_no"],
							$payload["high_score"],
							$payload["achieved_at"],
						);
						if (!$ok) {
							throw new \Exception("Failed to save unit score");
						}
						break;

					case "user_setting":
						logger()->debug('Processing user_settings event: ' . $event["event_id"]);
						if (!isset($payload["setting_key"]) || !isset($payload["value"])) {
							throw new \Exception("Missing required fields: setting_key or value");
						}
						$ok = $this->userSettingsRepository->save(
							$userId,
							$payload["setting_key"],
							$payload["value"],
						);
						if (!$ok) {
							throw new \Exception("Failed to save user settings");
						}
						break;

					default:
						throw new \Exception(
							"Unknown event type: " . $event["type"]
						);
				}

				$processedIds[] =
					$event["event_id"];
			}

			$this->db->commit();

			return $processedIds;
		} catch (Throwable $e) {

			if ($this->db->inTransaction()) {
				$this->db->rollBack();
			}

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
