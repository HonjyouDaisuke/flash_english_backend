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
			['userId' => $userId, 'count' => $input]
		);

		if (!$input) {
			http_response_code(400);
			echo json_encode([
				"error" => "Invalid JSON"
			]);
			return;
		}

		if (!isset($input["events"])) {
			http_response_code(400);
			echo json_encode([
				"error" => "events required"
			]);
			return;
		}
		logger()->debug('-------------');
		if (!$input) {
			http_response_code(400);
			echo json_encode(["error" => "Invalid JSON"]);
			return;
		}
		logger()->debug('Start sync process...', ['input' => $input]);
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
			echo json_encode(["error" => $e->getMessage()]);
		}
	}
}
