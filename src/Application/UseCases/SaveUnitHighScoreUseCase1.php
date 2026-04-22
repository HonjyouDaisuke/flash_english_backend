<?php

namespace App\Application\UseCases;

use App\Repositories\UnitHighScoreRepository;
use App\Services\JwtService;

class SaveUnitHighScoreUseCase
{
	private UnitHighScoreRepository $repo;

	public function __construct(UnitHighScoreRepository $repo)
	{
		$this->repo = $repo;
	}

	public function execute(string $userId, array $data): void
	{
		file_put_contents("debug.log", "DB access start." . $data["achieved_at"] . "\n", FILE_APPEND);
		$this->repo->save(
			$userId,
			$data["category_id"],
			$data["unit_id"],
			$data["score"],
			$data["achieved_at"],
		);
	}
}
