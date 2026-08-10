<?php

namespace App\Controllers;

use App\Application\UseCases\SaveStudyLogUseCase;
use App\Application\UseCases\GetStudyLogUseCase;

class StudyLogController
{
	private SaveStudyLogUseCase $saveUseCase;
	private GetStudyLogUseCase $getUseCase;
	private string $logFile = __DIR__ . "/../../public/debug.log";

	public function __construct(SaveStudyLogUseCase $saveUseCase, GetStudyLogUseCase $getUseCase)
	{
		$this->saveUseCase = $saveUseCase;
		$this->getUseCase = $getUseCase;
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
			$this->saveUseCase->execute($userId, $input);
			echo json_encode(["success" => "true"]);
		} catch (\Exception $e) {
			http_response_code(400);
			http_response_code(400);
			echo json_encode(["error" => $e->getMessage()]);
		}
	}

	public function getSince(string $userId, ?string $since): void
	{
		try {
			$logs = $this->getUseCase->getSince($userId, $since);
			echo json_encode(["success" => true, "logs" => $logs]);
		} catch (\Exception $e) {
			http_response_code(400);
			echo json_encode(["error" => "internal server error"]);
		}
	}
}
