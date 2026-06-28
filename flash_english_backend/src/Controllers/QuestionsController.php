<?php

namespace App\Controllers;

use App\Application\UseCases\GetAllQuestionsUseCase;

class QuestionsController
{
	private GetAllQuestionsUseCase $useCase;
	public function __construct(GetAllQuestionsUseCase $useCase)
	{
		$this->useCase = $useCase;
	}

	public function getAll(): void
	{
		try {
			$questions = $this->useCase->getAll();
			http_response_code(200);
			echo json_encode(["questions" => $questions]);
		} catch (\Exception $e) {
			http_response_code(400);
			echo json_encode(["error" => $e->getMessage()]);
		}
	}
}
