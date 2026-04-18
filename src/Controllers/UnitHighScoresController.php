<?php

namespace App\Controllers;

use App\Application\UseCases\SaveUnitHighScoreUseCase;

class UnitHighScoresController
{
	private SaveUnitHighScoreUseCase $useCase;
	private string $logFile = __DIR__ . "/../../public/debug.log";

	public function __construct(SaveUnitHighScoreUseCase $useCase)
	{
		$this->useCase = $useCase;
	}

	public function save(string $userId): void
	{
		$input = json_decode(file_get_contents("php://input"), true);
		if (!$input) {
			http_response_code(400);
			echo json_encode(["error" => "Invalid JSON"]);
			return;
		}

		try {
			$this->useCase->execute($userId, $input);
			echo json_encode(["success" => "true"]);
		} catch (\Exception $e) {
			http_response_code(400);
			http_response_code(400);
			echo json_encode(["error" => $e->getMessage()]);
		}
	}
}
