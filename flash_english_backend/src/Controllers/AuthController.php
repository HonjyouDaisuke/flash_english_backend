<?php

namespace App\Controllers;

use App\Application\UseCases\GoogleLoginUseCase;

class AuthController
{
	private GoogleLoginUseCase $useCase;

	public function __construct(GoogleLoginUseCase $useCase)
	{
		$this->useCase = $useCase;
	}

	public function google(): void
	{
		$input = json_decode(file_get_contents("php://input"), true);

		try {
			$result = $this->useCase->execute($input["id_token"]);
			echo json_encode($result);
		} catch (\Exception $e) {
			http_response_code(401);
			echo json_encode(["error" => $e->getMessage()]);
		}
	}
}
