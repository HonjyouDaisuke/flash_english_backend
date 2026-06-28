<?php

namespace App\Controllers;

use App\Application\UseCases\GetAllCategoriesUseCase;

class CategoriesController
{
	private GetAllCategoriesUseCase $useCase;

	public function __construct(GetAllCategoriesUseCase $useCase)
	{
		$this->useCase = $useCase;
	}

	public function getAll(): void
	{
		try {
			$categories = $this->useCase->getAll();
			http_response_code(200);
			echo json_encode(["categories" => $categories]);
		} catch (\Exception $e) {
			http_response_code(500);
			logger()->debug("categories fetch error : {$e->getMessage()}");
			echo json_encode(["error" => "categories fetch error!"]);
		}
	}
}
