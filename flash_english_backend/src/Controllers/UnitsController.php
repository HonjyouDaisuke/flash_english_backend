<?php

namespace App\Controllers;

use App\Application\UseCases\GetAllUnitsUseCase;

class UnitsController
{
	private GetAllUnitsUseCase $useCase;

	public function __construct(GetAllUnitsUseCase $useCase)
	{
		$this->useCase = $useCase;
	}

	public function getAll(): void
	{
		try {
			$units = $this->useCase->getAll();
			http_response_code(200);
			echo json_encode(["units" => $units]);
		} catch (\Exception $e) {
			http_response_code(400);
			echo json_encode(["error" => $e->getMessage()]);
		}
	}
}
