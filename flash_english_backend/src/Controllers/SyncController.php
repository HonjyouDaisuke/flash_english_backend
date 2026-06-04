<?php

namespace App\Controllers;

use App\Application\UseCases\Sync;
use App\Application\UseCases\SyncUseCase;

class SyncController
{
	private SyncUseCase $useCase;

	public function __construct(SyncUseCase $useCase)
	{
		$this->useCase = $useCase;
	}

	public function sync(string $userId): void
	{
		$input = json_decode(file_get_contents("php://input"), true);

		logger()->debug(
			'Received sync request',
			['userId' => $userId, 'count' => count($input["events"] ?? [])]
		);

		if ($input === null && json_last_error() !== JSON_ERROR_NONE) {
			http_response_code(400);
			echo json_encode([
				"error" => "Invalid JSON"
			]);
			return;
		}

		if (!isset($input["events"]) || !is_array($input["events"])) {
			http_response_code(400);
			echo json_encode([
				"error" => "events array required"
			]);
			return;
		}

		logger()->debug('Start sync process...', ['input' => $input["events"]]);
		try {
			$result = $this->useCase->execute(
				$userId,
				$input["events"],
			);
			echo json_encode([
				"success" => true,
				"result" => $result,
			]);
		} catch (\Exception $e) {
			http_response_code(400);
			echo json_encode(["error" => "internal server error"]);
		}
	}
}
