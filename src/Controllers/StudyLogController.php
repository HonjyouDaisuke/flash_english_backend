<?php

namespace App\Controllers;

use App\Application\UseCases\SaveStudyLogUseCase;

class StudyLogController
{
	private SaveStudyLogUseCase $useCase;
	private string $logFile = __DIR__ . "/../../public/debug.log";

	public function __construct(SaveStudyLogUseCase $useCase)
	{
		$this->useCase = $useCase;
	}

	public function save(): void
	{
		$auth = $_SERVER["HTTP_AUTHORIZATION"] ?? null;
		if (!$auth) {
			http_response_code(401);
			echo json_encode(["error" => "Unauthorized"]);
			return;
		}
		$token = str_replace("Bearer ", "", $auth);
		$input = json_decode(file_get_contents("php://input"), true);
		if (!$input) {
			http_response_code(400);
			echo json_encode(["error" => "Invalid JSON"]);
			return;
		}

		try {
			$this->useCase->execute($token, $input);
			echo json_encode(["success" => "true"]);
		} catch (\Exception $e) {
			http_response_code(400);
			http_response_code(400);
			echo json_encode(["error" => $e->getMessage()]);
		}
	}
}
