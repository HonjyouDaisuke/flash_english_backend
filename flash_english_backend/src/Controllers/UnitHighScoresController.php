<?php

namespace App\Controllers;

use App\Application\UseCases\GetUnitHighScoreUseCase;
use App\Application\UseCases\SaveUnitHighScoreUseCase;

class UnitHighScoresController
{
	private SaveUnitHighScoreUseCase $saveUseCase;
	private GetUnitHighScoreUseCase $getUseCase;
	// private string $logFile = __DIR__ . "/../../public/debug.log";

	public function __construct(SaveUnitHighScoreUseCase $saveUseCase, GetUnitHighScoreUseCase $getUseCase)
	{
		$this->saveUseCase = $saveUseCase;
		$this->getUseCase = $getUseCase;
	}

	public function save(string $userId): void
	{
		logger()->debug('Start Save...');
		$input = json_decode(file_get_contents("php://input"), true);
		if (!$input) {
			http_response_code(400);
			echo json_encode(["error" => "Invalid JSON"]);
			return;
		}
		logger()->debug('DB save...');
		try {
			$this->saveUseCase->execute($userId, $input);
			echo json_encode(["success" => "true"]);
		} catch (\Exception $e) {
			http_response_code(400);
			http_response_code(400);
			echo json_encode(["error" => $e->getMessage()]);
		}
	}

	public function get(string $userId): void
	{
		$input = json_decode(file_get_contents("php://input"), true);
		logger()->debug('Start score_load...');
		if (!$input) {
			http_response_code(400);
			echo json_encode(["error" => "Invalid JSON"]);
			return;
		}
		logger()->debug('Load success...');
		try {
			$result = $this->getUseCase->get($userId, $input);
			logger()->debug('Loaded data', is_array($result) ? $result : ['result' => $result]);
			echo json_encode($result);
		} catch (\Exception $e) {
			http_response_code(400);
			echo json_encode([
				"error" => $e->getMessage()
			]);
		}
	}

	public function getAll(string $userId): void
	{
		$input = json_decode(file_get_contents("php://input"), true);
		logger()->debug('Start score_load...');
		if (!$input) {
			http_response_code(400);
			echo json_encode(["error" => "Invalid JSON"]);
			return;
		}
		logger()->debug('Load success...');
		try {
			$result = $this->getUseCase->getAll($userId, $input);
			logger()->debug('Loaded data...');
			echo json_encode($result);
		} catch (\Exception $e) {
			http_response_code(400);
			echo json_encode([
				"error" => $e->getMessage()
			]);
		}
	}
}
